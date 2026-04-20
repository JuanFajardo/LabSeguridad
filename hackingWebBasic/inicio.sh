docker build -t webtest .

# Ejecutar el contenedor
docker run -d \
  --name webtest1 \
  -p 8080:80 \
  -p 3307:3306 \
  webtest

# Verificar que todo funciona
docker exec -it webtest1 bash
