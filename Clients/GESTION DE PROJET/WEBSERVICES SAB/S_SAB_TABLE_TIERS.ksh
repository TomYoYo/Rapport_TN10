#!/bin/env ksh
#===========================================================================
# S_SAB_TABLE_TIERS *** 31/03/14 *** 14/04/14 *** SAB (Florent Gayet)
#================================== Objet ==================================
# TIERS - Traitement
# Traitement vidant les tables de données liées au tiers
#============================== Modifications ==============================
# user - jj/mm/aa - Libelle modification
# Florent Gayet - 03/04/2014 - Mise à jour de la requête de chargement du fichier
# Perrine LEOST - 14/04/2014 - Modification du nom de la base SQL
#===========================================================================

debug=0
verbose=0

function usage {
	[[ ${debug} > 0 ]] && set -vx
	echo ""
	echo "usage: ${SCRIPT} [-h] [-v] [-d] ( -i fichier_entree | -e ) -t table"
	echo ""
	echo "        -h: affiche cette aide"
	echo "        -v: mode verbeux. Affiche des informations supplementaires"
	echo "        -d: mode denbug: ajoute un set -vx"
	echo "        -f fichier_entree: precise le nom du fichier en entree qui contient les donnees et doit etre charge (exclusif avec -e)"
	echo "        -e: specifie qu'il faut remetre a vide la table (exclusif avec -f)"
	echo "        -t table: precise le nom de la table ou charger le fichier en entree"
	echo ""
	echo "        Il faut preciser ou -f fichier_entree ou -e suivant l'action desiree"
	echo "        Le script verifie que la table existe et est vide avant d'inserer les donnees"
	echo ""
	echo "Liste des codes de retour:"
	echo "          0 : OK"
	echo "         11 : L'option a besoin d'un parametre"
	echo "         12 : L'option est inconnue"
	echo "         13 : Ni l'option -f ni l'option -e n'est presente"
	echo "         14 : L'option -t est obligatoire"
	echo "         21 : Le fichier en entree n'existe pas ou n'est pas accessible pour l'utilisateur"
	echo "         22 : Le fichier en entree n'existe pas ou n'est pas un fichier"
	echo "         23 : Le fichier en entree n'est pas lisible pour l'utilisateur"
	echo "         31 : Le repertoire temporaire n'existe pas, n'est pas un repertoire ou n'est pas accessible pour l'utilisateur"
	echo "         32 : L'utilisateur n'a pas les droits de lecture sur le repertoire temporaire"
	echo "         33 : L'utilisateur n'a pas les droits d'ecriture sur le repertoire temporaire"
	echo "         41 : Le script n'arrive pas a se connecter a Oracle"
	echo "         42 : La table existe mais n'est pas vide"
	echo "         43 : La table n'existe pas"
	echo "         44 : Le script a une erreur lors de la verification de la table"
	echo "         51 : sqlldr a eu une erreur lors du chargement"
	echo "        240 : sqlplus n'est pas accessible"
	echo "  250 a 255 : erreur liee au developpement du script: blame developper"
	echo ""
}

function erreur {
	[[ ${debug} > 0 ]] && set -vx
	echo ""
	echo "erreur: ${1:-Pas de message}"
	if [[ ! -z "$3" ]]; then
		usage
	fi
	echo ""
	if (( ${2:-255} != 0 )); then
		echo "Le script se termine avec une erreur: ${2:-255}" 1>&2
	else
		echo "Le script se termine sans erreur" 1>&2
	fi
	echo ""
	menage
	exit ${2:-255}
}

function menage {
	[[ ${debug} > 0 ]] && set -vx
	rm -f "${MY_TEMP}/${SCRIPT}".$$.*
}
trap 'menage' 0 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32
SCRIPT=$0
REPERTOIRE="${SCRIPT%/*}"
SCRIPT="${SCRIPT##*/}"
[[ "${REPERTOIRE}" == "${SCRIPT}" ]] && REPERTOIRE=${PWD}

# Definition de la chaine de connexion Oracle
ORACLE_CONNEXION=/@SAB
if [[ $(hostname) = 'LXLYOSAB32' ]]
then
  ORACLE_CONNEXION=SAB139/uss3zRDWiIl0_h@SABDEV
elif  [[ $(hostname) = 'LXLYOSAB20' ]]
then
  ORACLE_CONNEXION=/@SAB_P
fi


# Definition des valeur par defaut
verbose=0
debug=0
fichier_entree=""
table_bdd=""
action=""

# recuperation des options passee en mligne de commande
while getopts ":vdef:t:h" option; do
	case "${option}" in
		(v) verbose=1;;
		(d) debug=1;;
		(f) fichier_entree="${OPTARG}";;
		(t) table_bdd="${OPTARG}";;
		(h) usage; exit 0;;
		(e) action="erase";;
		(:) erreur "Option (${OPTARG}) a besoin d'un parametre" 11 usage;;
		(?) erreur "Option (${OPTARG}) est inconnue" 12 usage;;
		(*) erreur "Erreur interne pour getopts: blame developper" 250;;
	esac
done

[[ ${debug} > 0 ]] && set -vx

# Verifie les options entre-elles
[[ ${verbose:-0} > 0 ]] && echo "    Verifie presence option -f fichier_entree ou -e"
if [[ -z "${fichier_entree}" ]]; then
	if [[ -z "${action}" ]]; then
		erreur "Au moins une option -f fichier_entree ou -e doit être presente" 13 usage
	fi
else
	# Verifie le fichier passe en entree (option -i)
	[[ ${verbose:-0} > 0 ]] && echo "    Verifie le fichier passe en parametre (${fichier_entree})"
	if [[ ! -e "${fichier_entree}" ]]; then
		erreur "Le fichier specifie en entree (${fichier_entree}) n'existe pas ou  l'utilisateur ($(id)) n'a pas le droit d'y arriver" 21 usage
	fi
	if [[ ! -f "${fichier_entree}" ]]; then
		erreur "Le fichier specifie en entree (${fichier_entree}) n'existe pas ou n'est pas un fichier" 22 usage
	fi
	if [[ ! -r "${fichier_entree}" ]]; then
		erreur "Le fichier specifie en entree (${fichier_entree}) n'est pas lisible" 23 usage
	fi
fi

[[ ${verbose:-0} > 0 ]] && echo "    Verifie presence table (option -t)"
if [[ -z "${table_bdd}" ]]; then
	erreur "La table (option -t) doit toujours etre specifiee" 14 usage
fi

# Trouve le repertoire temporaire
[[ ${verbose:-0} > 0 ]] && echo "    Verifie repertoire temporaire"
MY_TEMP=${TEMP:-${TMP:-/tmp}}
if [[ ! -d "${MY_TEMP}" ]]; then
	erreur "Le repertoire temporaire (${MY_TEMP} par defaut \$TEMP ou \$TMP ou /tmp) n'existe pas. Utilisez l'option -T" 31
fi
if [[ ! -r "${MY_TEMP}" ]]; then
	erreur "L'utilisateur ($(id)) n'a pas le droit de lecture sur le repertoire temporaire (${MY_TEMP} par defaut \$TEMP ou \$TMP ou /tmp). Utilisez l'option -T" 32
fi
if [[ ! -w "${MY_TEMP}" ]]; then
	erreur "L'utilisateur ($(id)) n'a pas le droit d'ecriture sur le repertoire temporaire (${MY_TEMP} par defaut \$TEMP ou \$TMP ou /tmp). Utilisez l'option -T" 33
fi

# Definit les fichiers temporaires
[[ ${verbose:-0} > 0 ]] && echo "    Definie les fichiers temporaires"
fichier_temporaire_1=${MY_TEMP}/${SCRIPT}.$$.1
> "${fichier_temporaire_1}"
if (( $? > 0 )); then
	erreur "Le script ne peut creer le fichier (${fichier_temporaire_1}): pas d'esapce ou blame developper" 251 
fi
fichier_temporaire_2=${MY_TEMP}/${SCRIPT}.$$.2
> "${fichier_temporaire_2}"
if (( $? > 0 )); then
	erreur "Le script ne peut creer le fichier (${fichier_temporaire_2}): pas d'espace ou blame developper" 251 
fi
fichier_temporaire_3=${MY_TEMP}/${SCRIPT}.$$.3
> "${fichier_temporaire_3}"
if (( $? > 0 )); then
	erreur "Le script ne peut creer le fichier (${fichier_temporaire_3}): pas d'espace ou blame developper" 251 
fi

# Verifie la connexion a la base
sqlplus /nolog >  "${fichier_temporaire_1}" 2>&1 <<-fin_script_sql
		WHENEVER SQLERROR EXIT 1
		WHENEVER OSERROR EXIT 2
		CONNECT ${ORACLE_CONNEXION}
		EXIT 0
	fin_script_sql
code_de_retour=$?
case "${code_de_retour}" in
	(0) ;;
	(127) erreur "sqlplus n'a pas ete trouve dans le PATH" 240;;
	(*) cat "${fichier_temporaire_1}"; erreur "Le script ne peut pas se connecter a la base Oracle) (error=${code_de_retour})" 41;;
esac

if [[ "${action}" == "erase" ]]; then
	# Vide la table passee en entree (option -t)
	[[ ${verbose:-0} > 0 ]] && echo "    Vide la table passee en parametre (${table_bdd})"
	sqlplus /nolog > "${fichier_temporaire_1}" 2>&1 <<-fin_script_sql
			CONNECT ${ORACLE_CONNEXION}
			VARIABLE code_retour NUMBER
			DEFINE code_retour=15
			DECLARE
				Table_Non_Trouvee EXCEPTION;
				PRAGMA EXCEPTION_INIT(Table_Non_Trouvee, -942);
			BEGIN
				:code_retour := 20;
				EXECUTE IMMEDIATE 'DELETE FROM ${table_bdd}';
				:code_retour := 0;
			EXCEPTION
				WHEN NO_DATA_FOUND THEN
					:code_retour := 0;
				WHEN Table_Non_Trouvee THEN
					:code_retour := 22;
				WHEN OTHERS THEN
					dbms_output.put_line(SQLCODE);
					dbms_output.put_line(SQLERRM);
			END;
			/
			EXIT :code_retour;
		fin_script_sql
	code_de_retour=$?
	case "${code_de_retour}" in
		(0) ;;
		(22) erreur "La table (${table_bdd}) n'existe pas" 43;;
		(127) erreur "sqlplus n'a pas ete trouve dans le PATH" 240;;
		(*) cat "${fichier_temporaire_1}"; erreur "Le script ne peut pas verifier l'existence de la table (${table_bdd}) (error=${code_de_retour}). Blame developper" 44;;
	esac
else
	# Verifie la table passee en entree (option -t)
	[[ ${verbose:-0} > 0 ]] && echo "    Verifie la table passee en parametre (${table_bdd})"
	sqlplus /nolog > "${fichier_temporaire_1}" 2>&1 <<-fin_script_sql
			CONNECT ${ORACLE_CONNEXION}
			VARIABLE code_retour NUMBER
			VARIABLE nb_lignes NUMBER
			DEFINE code_retour=15
			DECLARE
				Table_Non_Trouvee EXCEPTION;
				PRAGMA EXCEPTION_INIT(Table_Non_Trouvee, -942);
			BEGIN
				:code_retour := 20;
				EXECUTE IMMEDIATE 'SELECT COUNT(*) FROM ${table_bdd}' INTO :nb_lignes;
				:code_retour:=24;
				IF :nb_lignes = 0 THEN
					:code_retour:=0;
				END IF;
			EXCEPTION
				WHEN Table_Non_Trouvee THEN
					:code_retour := 22;
				WHEN OTHERS THEN
					dbms_output.put_line(SQLERRM);
			END;
			/
			EXIT :code_retour;
		fin_script_sql
	code_de_retour=$?
	case "${code_de_retour}" in
		(0) ;;
		(24) erreur "La table (${table_bdd}) existe mais n'est pas vide" 42;;
		(22) erreur "La table (${table_bdd}) n'existe pas" 43;;
		(127) erreur "sqlplus n'a pas ete trouve dans le PATH" 240;;
		(*) cat "${fichier_temporaire_1}"; erreur "Le script ne peut pas verifier l'existence de la table (${table_bdd}) (error=${code_de_retour}). Blame developper" 44;;
	esac

	# Prepare le fichier de controle pour le sqlldr
	[[ ${verbose:-0} > 0 ]] && echo "    Prepare le fichier de controle"
	cat > "${fichier_temporaire_1}" <<-fin_script_sql
		LOAD DATA
		APPEND PRESERVE BLANKS
		INTO TABLE ${table_bdd}
		TRAILING NULLCOLS
		(
			MNWFLTORI       POSITION        (  1:   1) CHAR,
			MNWFLTREF       POSITION        (  2:  21) CHAR,
			MNWFLTNUM       POSITION        ( 22:  28) CHAR,
			MNWFLTSEQ       POSITION        ( 29:  30) CHAR,
			MNWFLTDAT       POSITION        ( 31:  38) CHAR,
			MNWFLTHEU       POSITION        ( 39:  46) CHAR,
			MNWFLTDON       POSITION        ( 47:5046) CHAR, 
			ID                      CONSTANT 1
		)
	fin_script_sql
	sqlldr "${ORACLE_CONNEXION}" control="${fichier_temporaire_1}" data="${fichier_entree}" log="${fichier_temporaire_2}" bad="${fichier_temporaire_3}" errors=0 discardmax=1
	code_de_retour=$?
	cat "${fichier_temporaire_2}"
	case ${code_de_retour} in
		(0);;
		(1|2) echo "Fichier BAD"; cat "${fichier_temporaire_3}"; erreur "Le script ne peut charger la table (${table_bdd}) avec le fichier (${fichier_entree}) (cr=${code_de_retour})" 51;;
		(*) echo "Fichier BAD"; cat "${fichier_temporaire_3}"; erreur "Une erreur est survenue au chargement de la table (${table_bdd}) avec le fichier (${fichier_entree}). Blame developper" 253;;
	esac
fi

# Fin normale
exit 0
