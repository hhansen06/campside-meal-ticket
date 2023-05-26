FROM mysql:latest

ADD . /
RUN chmod +x /bootstrap.sh
ENTRYPOINT ["/bootstrap.sh"]
