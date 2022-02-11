FROM kshivaprasad/java:latest

RUN mkdir -p /etc/acceleron/acceleron-services

WORKDIR /etc/acceleron/acceleron-services

ADD service/build/libs/*.jar .

CMD java $JAVA_OPTS  -jar *.jar

EXPOSE 8080
