FROM mysql:8
RUN echo "default-authentication-plugin=mysql_native_password" >> /etc/mysql/my.cnf
COPY films.sql /docker-entrypoint-initdb.d/
