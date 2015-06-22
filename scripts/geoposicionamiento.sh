#!/bin/bash
# Script para simular la entrada de coordenadas GPS
# Permite probar el rastreo de un elemento

# definicion de la ruta mediante sus coordenadas
# para el ejemplo la ruta desde echaurren 424 a echaurren 2 (Santiago, Chile)
COORDENADAS=(
	-33.4534492265306 -70.6661019346939 # 424
	-33.4529874 -70.6661237 # 400
	-33.4525147571429 -70.6661437 # 350
	-33.4513253163265 -70.6661933408163 # 250
	-33.4501576210526 -70.6662379315789 # 150
	-33.4478232 -70.6663664 # 2
)

# id
ID="56f8a730fce49ec65d1cc5f216462711"

# url
URL="http://intranet.dev/mapa/geoposicionamiento_update"

while true; do
	NCOORDENADAS=${#COORDENADAS[@]}
	i=0
	echo "Cargando coordenadas"
	while [ $i -lt $NCOORDENADAS ]; do
		# recuperar coordenadas
        	LAT=${COORDENADAS[$i]}
	        LON=${COORDENADAS[$i+1]}
		# cargar coordenadas
		echo $LAT, $LON;
		elinks "$URL?id=$ID&latitud=$LAT&longitud=$LON" > /dev/null
		# aumentar contador y esperar un tiempo
		let i=$i+2
		sleep 5;
	done;
done;

