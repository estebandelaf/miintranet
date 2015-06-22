#!/bin/bash

# Cargador automático de UF
# @author DeLaF, esteban[at]delaf.cl
# @version 2011-04-22

# configuracion base de datos
DB="psql intranet"

# verificar que se hayan pasado 2 parametros o nada
if [ $# -ne 0 -a $# -ne 2 ]; then
	echo "Modo de uso: $0 [anio] [mes]";
	exit
fi

# si se pasaron 2 parametros se asignan a ANIO y MES, sino se preguntara por el mes actual
if [ $# -eq 2 ]; then
	ANIO=$1
	MES=$2
else
	ANIO=`date +%Y`
	MES=`date +%m`
fi

# formatear url para consultar en SII
URL="http://www.sii.cl/pagina/valores/uf/uf$ANIO.htm"
# obtener valor de columna a extraer mediante awk
COL=`expr $MES + 2`

# ejecutar consulta a la url y filtrar valores de uf
VALORES=`elinks $URL | cat | egrep "^\|" | egrep -v "^\|-|^\| D" | awk -F '|' '{print $col}' col=$COL | egrep -v "^ "`

# audit
AUDIT_PROGRAMA='uf.sh'
AUDIT_USUARIO=`whoami`@`hostname`
AUDIT_FECHAHORA=`date "+%F %T"`

# mostrar los valores de uf consultados
DIA=1 # contador del dia
for VALOR in `echo $VALORES`; do # por cada valor consultado ejecutar el ciclo
	# modificaciones para notacion gringa de punto decimal y separador de miles
	VALOR=${VALOR/./} # quitar punto de separacion de miles
	VALOR=${VALOR/,/.} # cambiar coma por punto
	# mostrar valor de uf consultado
	echo $ANIO-$MES-$DIA $VALOR
	# insetar fecha en base de datos
	$DB << EOF
		INSERT INTO uf VALUES ('$ANIO-$MES-$DIA', '$VALOR', '$AUDIT_PROGRAMA', '$AUDIT_USUARIO', '$AUDIT_FECHAHORA');
EOF
	# aumentar contador del dia
	DIA=`expr $DIA + 1`
done
