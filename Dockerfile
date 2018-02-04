FROM php:7.2-zts
RUN apt-get update
RUN apt-get install git --yes
RUN git clone https://github.com/krakjoe/pthreads
RUN cd pthreads && phpize && ./configure
RUN cd pthreads && make && make install
RUN echo "extension=pthreads.so" > /usr/local/etc/php/conf.d/pthreads.ini
WORKDIR /project
