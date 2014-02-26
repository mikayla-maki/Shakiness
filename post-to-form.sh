#!/bin/bash

usage() { echo "Usage: $0 [-t <string>] [-d <string>] [-s <float>]" 1>&2; exit 1; }

while getopts “:t:d:s:” o; do
    case "${o}" in
        t)
            title=${OPTARG}
            ;;
        d)
            director=${OPTARG}
            ;;
        s)
            shakiness=${OPTARG}
            re='^[-]?[0-9]+([.][0-9]+)?$'
            if ! [[ $shakiness =~ $re ]] ; then 
                usage
            fi
            ;;	
        *)
            usage
            ;;
    esac
done

shift $((OPTIND-1))

if [ -z "${title}" ] || [ -z "${director}" ] || [ -z "${shakiness}" ]; then
    usage
fi

curl --data "title=${title}&director=${director}&shakiness=${shakiness}&sender=curl" http://trentonmaki.us/index.php

