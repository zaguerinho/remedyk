FROM php:5.6-apache-stretch
RUN apt-get update && apt-get install -y gnupg2
RUN apt-get install wget -y
RUN apt-get install build-essential libssl-dev libreadline-dev zlib1g-dev libcurl4-openssl-dev uuid-dev -y
WORKDIR /root
RUN wget --no-check-certificate https://ftp.postgresql.org/pub/source/v9.6.24/postgresql-9.6.24.tar.gz
RUN tar -xvzf postgresql-9.6.24.tar.gz
WORKDIR /root/postgresql-9.6.24
RUN ./configure
RUN make
RUN make install
RUN pgsql --version
RUN echo 'alias ll="ls -l"' >> ~/.bashrc

#Create the new user postgres to run the PostgreSQL processes. As the root user, execute this command:
#
#adduser postgres
#Initialize the database directory and start up PostgreSQL. As the root user, follow these steps:
#
#
#mkdir -p /usr/local/pgsql/data
#chown postgres /usr/local/pgsql/data
#su - postgres
#initdb -D /usr/local/pgsql/data -E UNICODE --locale=C
#pg_ctl -D /usr/local/pgsql/data -l /home/postgres/logfile start


