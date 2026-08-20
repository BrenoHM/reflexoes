#!/bin/sh
# Entrypoint da imagem de produção. Roda em foreground do início ao fim -
# nenhum passo aqui deve iniciar um processo em background e devolver o
# terminal, ou o Render entende que o serviço morreu.
set -e

# Permite sobrescrever o comando (ex.: `docker run <imagem> php artisan tinker`
# ou `bash` para depuração) sem passar pela configuração do Apache.
if [ "$#" -gt 0 ]; then
    exec "$@"
fi

PORT="${PORT:-8080}"

# O Render define a porta em tempo de execução; nunca é fixa no build. Os
# arquivos do Apache trazem o placeholder __PORT__ literal (ver
# docker/apache/), substituído aqui pelo valor real de $PORT.
sed -i "s/__PORT__/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# config:cache precisa rodar DEPOIS que as Environment Variables do Render já
# estão disponíveis no processo - por isso acontece aqui no startup, nunca
# durante o build da imagem (no build, DB_HOST/APP_KEY/etc. ainda não têm os
# valores reais de produção, e cachear isso quebraria a aplicação inteira).
php artisan config:cache
php artisan route:cache
php artisan view:cache

# O Apache trata SIGWINCH como "graceful stop" (é assim que apachectl
# implementa isso). Esse sinal pode chegar sem ninguém pedir, repassado por
# alguma camada de terminal/orquestração no meio do caminho (ex.:
# redimensionamento de janela em ambientes com pty anexado) - sem relação
# nenhuma com um shutdown real do container. Ignoramos WINCH aqui e só
# repassamos os sinais de parada de verdade (TERM/INT) para o Apache, para
# o serviço não cair por engano. tini (ENTRYPOINT) continua cuidando do
# reap de zumbis e do encaminhamento de sinais para este processo.
trap '' WINCH
apache2-foreground &
APACHE_PID=$!
trap 'kill -TERM $APACHE_PID 2>/dev/null' TERM INT
wait $APACHE_PID
