#!/usr/bin/env bash

# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
# IPP - syn - doplňkové testy - 2014/2015
# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
# Činnost: 
# - vytvoří výstupy studentovy úlohy v daném interpretu na základě sady testů
# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
# Popis (README):
#
# Struktura skriptu _stud_tests.sh (v kódování UTF-8):
# Každý test zahrnuje až 4 soubory (vstupní soubor, případný druhý vstupní 
# soubor, výstupní soubor, soubor logující chybové výstupy *.err vypisované na 
# standardní chybový výstup (pro ilustraci) a soubor logující návratový kód 
# skriptu *.!!!). Pro spuštění testů je nutné do stejného adresáře zkopírovat i 
# váš skript. V komentářích u jednotlivých testů jsou uvedeny dodatečné 
# informace jako očekávaný návratový kód. 
#
# Proměnné ve skriptu _stud_tests.sh pro konfiguraci testů:
#  INTERPRETER - využívaný interpret 
#  EXTENSION - přípona souboru s vaším skriptem (jméno skriptu je dáno úlohou) 
#  LOCAL_IN_PATH/LOCAL_OUT_PATH - testování různých cest ke vstupním/výstupním
#    souborům
#  
# Další soubory archivu s doplňujícími testy:
# V adresáři ref-out najdete referenční soubory pro výstup (*.out nebo *.xml), 
# referenční soubory s návratovým kódem (*.!!!) a pro ukázku i soubory s 
# logovaným výstupem ze standardního chybového výstupu (*.err). Pokud některé 
# testy nevypisují nic na standardní výstup nebo na standardní chybový výstup, 
# tak může odpovídající soubor v adresáři chybět nebo mít nulovou velikost.
#
# Doporučení a poznámky k testování:
# Tento skript neobsahuje mechanismy pro automatické porovnávání výsledků vašeho 
# skriptu a výsledků referenčních (viz adresář ref-out). Pokud byste rádi 
# využili tohoto skriptu jako základ pro váš testovací rámec, tak doporučujeme 
# tento mechanismus doplnit.
# Dále doporučujeme testovat různé varianty relativních a absolutních cest 
# vstupních a výstupních souborů, k čemuž poslouží proměnné začínající 
# LOCAL_IN_PATH a LOCAL_OUT_PATH (neomezujte se pouze na zde uvedené triviální 
# varianty). 
# Výstupní soubory mohou při spouštění vašeho skriptu již existovat a pokud není 
# u zadání specifikováno jinak, tak se bez milosti přepíší!           
# Výstupní soubory nemusí existovat a pak je třeba je vytvořit!
# Pokud běh skriptu skončí s návratovou hodnotou různou od nuly, tak není 
# vytvoření souboru zadaného parametrem --output nutné, protože jeho obsah 
# stejně nelze považovat za validní.
# V testech se jako pokaždé určitě najdou nějaké chyby nebo nepřesnosti, takže 
# pokud nějakou chybu najdete, tak na ni prosím upozorněte ve fóru příslušné 
# úlohy (konstruktivní kritika bude pozitivně ohodnocena). Vyhrazujeme si také 
# právo testy měnit, opravovat a případně rozšiřovat, na což samozřejmě 
# upozorníme na fóru dané úlohy.
#
# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

TASK="../syn"
INTERPRETER="php"
EXTENSION=php
#INTERPRETER=python3
#EXTENSION=py

# cesty ke vstupním a výstupním souborům
LOCAL_IN_PATH="./" # (simple relative path)
LOCAL_IN_PATH2="" #Alternative 1 (primitive relative path)
LOCAL_IN_PATH3=`pwd`"/" #Alternative 2 (absolute path)
LOCAL_OUT_PATH="./moj-out/" # (simple relative path)
LOCAL_OUT_PATH2="./moj-out/" #Alternative 1 (primitive relative path)
LOCAL_OUT_PATH3=`pwd`"/moj-out/" #Alternative 2 (absolute path)
# cesta pro ukládání chybového výstupu studentského skriptu
LOG_PATH="./moj-out/"
COUNT=0


# test01: Argument error; Expected output: test01.out; Expected return code: 1
$INTERPRETER $TASK.$EXTENSION --error 2> ${LOG_PATH}test01.err
echo -n $? > ${LOG_PATH}test01.!!!

# test02: Input error; Expected output: test02.out; Expected return code: 2
$INTERPRETER $TASK.$EXTENSION --input=nonexistent --output=${LOCAL_OUT_PATH3}test02.out 2> ${LOG_PATH}test02.err
echo -n $? > ${LOG_PATH}test02.!!!

# test03: Output error; Expected output: test03.out; Expected return code: 3
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}empty --output=nonexistent/${LOCAL_OUT_PATH2}test03.out 2> ${LOG_PATH}test03.err
echo -n $? > ${LOG_PATH}test03.!!!

# test04: Format table error - nonexistent parameter; Expected output: test04.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}empty --output=${LOCAL_OUT_PATH}test04.out --format=error-parameter.fmt 2> ${LOG_PATH}test04.err
echo -n $? > ${LOG_PATH}test04.!!!

# test05: Format table error - size; Expected output: test05.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH}empty --output=${LOCAL_OUT_PATH3}test05.out --format=error-size.fmt 2> ${LOG_PATH}test05.err
echo -n $? > ${LOG_PATH}test05.!!!

# test06: Format table error - color; Expected output: test06.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}empty --output=${LOCAL_OUT_PATH}test06.out --format=error-color.fmt 2> ${LOG_PATH}test06.err
echo -n $? > ${LOG_PATH}test06.!!!

# test07: Format table error - RE syntax; Expected output: test07.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}empty --output=${LOCAL_OUT_PATH3}test07.out --format=error-re.fmt 2> ${LOG_PATH}test07.err
echo -n $? > ${LOG_PATH}test07.!!!

# test08: Empty files; Expected output: test08.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}empty --output=${LOCAL_OUT_PATH3}test08.out --format=empty 2> ${LOG_PATH}test08.err
echo -n $? > ${LOG_PATH}test08.!!!

# test09: Format parameters; Expected output: test09.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}basic-parameter.in --output=${LOCAL_OUT_PATH3}test09.out --format=basic-parameter.fmt 2> ${LOG_PATH}test09.err
echo -n $? > ${LOG_PATH}test09.!!!

# test10: Argument swap; Expected output: test10.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --format=basic-parameter.fmt --output=${LOCAL_OUT_PATH3}test10.out --input=${LOCAL_IN_PATH}basic-parameter.in 2> ${LOG_PATH}test10.err
echo -n $? > ${LOG_PATH}test10.!!!

# test11: Standard input/output; Expected output: test11.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --format=basic-parameter.fmt >${LOCAL_OUT_PATH3}test11.out <${LOCAL_IN_PATH}basic-parameter.in 2> ${LOG_PATH}test11.err
echo -n $? > ${LOG_PATH}test11.!!!

# test12: Basic regular expressions; Expected output: test12.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}basic-re.in --output=${LOCAL_OUT_PATH3}test12.out --format=basic-re.fmt 2> ${LOG_PATH}test12.err
echo -n $? > ${LOG_PATH}test12.!!!

# test13: Special regular expressions; Expected output: test13.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}special-re.in --output=${LOCAL_OUT_PATH3}test13.out --format=special-re.fmt 2> ${LOG_PATH}test13.err
echo -n $? > ${LOG_PATH}test13.!!!

# test14: Special RE - symbols; Expected output: test14.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}special-symbols.in --output=${LOCAL_OUT_PATH2}test14.out --format=special-symbols.fmt 2> ${LOG_PATH}test14.err
echo -n $? > ${LOG_PATH}test14.!!!

# test15: Negation; Expected output: test15.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH}negation.in --output=${LOCAL_OUT_PATH3}test15.out --format=negation.fmt 2> ${LOG_PATH}test15.err
echo -n $? > ${LOG_PATH}test15.!!!

# test16: Multiple format parameters; Expected output: test16.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}multiple.in --output=${LOCAL_OUT_PATH3}test16.out --format=multiple.fmt 2> ${LOG_PATH}test16.err
echo -n $? > ${LOG_PATH}test16.!!!

# test17: Spaces/tabs in format parameters; Expected output: test17.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}multiple.in --output=${LOCAL_OUT_PATH3}test17.out --format=spaces.fmt 2> ${LOG_PATH}test17.err
echo -n $? > ${LOG_PATH}test17.!!!

# test18: Line break tag; Expected output: test18.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}newlines.in --output=${LOCAL_OUT_PATH}test18.out --format=empty --br 2> ${LOG_PATH}test18.err
echo -n $? > ${LOG_PATH}test18.!!!

# test19: Overlap; Expected output: test19.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}overlap.in --output=${LOCAL_OUT_PATH3}test19.out --format=overlap.fmt 2> ${LOG_PATH}test19.err
echo -n $? > ${LOG_PATH}test19.!!!

# test20: Perl RE; Expected output: test20.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}special-symbols.in --output=${LOCAL_OUT_PATH3}test20.out --format=re.fmt 2> ${LOG_PATH}test20.err
echo -n $? > ${LOG_PATH}test20.!!!

# test21: Example; Expected output: test21.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in --br --format=example.fmt > ${LOCAL_OUT_PATH3}test21.out 2> ${LOG_PATH}test21.err
echo -n $? > ${LOG_PATH}test21.!!!

# test22: Simple C program; Expected output: test22.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}cprog.c --br --format=c.fmt > ${LOCAL_OUT_PATH2}test22.out 2> ${LOG_PATH}test22.err
echo -n $? > ${LOG_PATH}test22.!!!

# test23: UTF-8 basics; Expected output: test23.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}utf8-basic.in --br --format=utf8-basic.fmt > ${LOCAL_OUT_PATH2}test23.out 2> ${LOG_PATH}test23.err
echo -n $? > ${LOG_PATH}test23.!!!

# test24: UTF-8 special; Expected output: test24.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}utf8-special.in --format=utf8-special.fmt > ${LOCAL_OUT_PATH2}test24.out 2> ${LOG_PATH}test24.err
echo -n $? > ${LOG_PATH}test24.!!!

# test25: Symbolic relative adress; Expected output: test25.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=./moj-out/./../utf8-special.in > ${LOCAL_OUT_PATH2}test25.out 2> ${LOG_PATH}test25.err
echo -n $? > ${LOG_PATH}test25.!!!

# test26: Nested html tags; Expected output: test26.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}nested.in  --format=nested.fmt > ${LOCAL_OUT_PATH2}test26.out 2> ${LOG_PATH}test26.err
echo -n $? > ${LOG_PATH}test26.!!!

# test27: Metachar \\; Expected output: test27.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}meta1.in  --format=meta1.fmt > ${LOCAL_OUT_PATH2}test27.out 2> ${LOG_PATH}test27.err
echo -n $? > ${LOG_PATH}test27.!!!

# test28: Metachar //; Expected output: test28.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}meta2.in  --format=meta2.fmt > ${LOCAL_OUT_PATH2}test28.out 2> ${LOG_PATH}test28.err
echo -n $? > ${LOG_PATH}test28.!!!

# test29: ALL Metachars togheter //; Expected output: test29.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}meta3.in  --format=meta3.fmt > ${LOCAL_OUT_PATH2}test29.out 2> ${LOG_PATH}test29.err
echo -n $? > ${LOG_PATH}test29.!!!

# test30: Wrong Format1; Expected output: test30.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err1.fmt > ${LOCAL_OUT_PATH2}test30.out 2> ${LOG_PATH}test30.err
echo -n $? > ${LOG_PATH}test30.!!!

# test31: Wrong Format2; Expected output: test31.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err2.fmt > ${LOCAL_OUT_PATH2}test31.out 2> ${LOG_PATH}test31.err
echo -n $? > ${LOG_PATH}test31.!!!

# test32: Wrong Format3; Expected output: test32.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err3.fmt > ${LOCAL_OUT_PATH2}test32.out 2> ${LOG_PATH}test32.err
echo -n $? > ${LOG_PATH}test32.!!!

# test33: Wrong Format3; Expected output: test33.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err4.fmt > ${LOCAL_OUT_PATH2}test33.out 2> ${LOG_PATH}test33.err
echo -n $? > ${LOG_PATH}test33.!!!

# test34: Wrong Format3; Expected output: test34.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err5.fmt > ${LOCAL_OUT_PATH2}test34.out 2> ${LOG_PATH}test34.err
echo -n $? > ${LOG_PATH}test34.!!!

# test35: Wrong Format3; Expected output: test35.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err6.fmt > ${LOCAL_OUT_PATH2}test35.out 2> ${LOG_PATH}test35.err
echo -n $? > ${LOG_PATH}test35.!!!

# test36: Wrong Format3; Expected output: test36.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err7.fmt > ${LOCAL_OUT_PATH2}test36.out 2> ${LOG_PATH}test36.err
echo -n $? > ${LOG_PATH}test36.!!!

# test37: Wrong Format3; Expected output: test37.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err8.fmt > ${LOCAL_OUT_PATH2}test37.out 2> ${LOG_PATH}test37.err
echo -n $? > ${LOG_PATH}test37.!!!

# test41: Wrong Format3; Expected output: test41.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err9.fmt > ${LOCAL_OUT_PATH2}test41.out 2> ${LOG_PATH}test41.err
echo -n $? > ${LOG_PATH}test41.!!!

# test38: Wrong Format3; Expected output: test38.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err10.fmt > ${LOCAL_OUT_PATH2}test38.out 2> ${LOG_PATH}test38.err
echo -n $? > ${LOG_PATH}test38.!!!

# test39: Wrong Format3; Expected output: test39.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err11.fmt > ${LOCAL_OUT_PATH2}test39.out 2> ${LOG_PATH}test39.err
echo -n $? > ${LOG_PATH}test39.!!!

# test40: Wrong Format3; Expected output: test40.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err12.fmt > ${LOCAL_OUT_PATH2}test40.out 2> ${LOG_PATH}test40.err
echo -n $? > ${LOG_PATH}test40.!!!

# test42: Wrong Format3; Expected output: test42.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}nothchar.in  --format=nothchar.fmt > ${LOCAL_OUT_PATH2}test42.out 2> ${LOG_PATH}test42.err
echo -n $? > ${LOG_PATH}test42.!!!

# test43: Wrong Format3; Expected output: test43.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err13.fmt > ${LOCAL_OUT_PATH2}test43.out 2> ${LOG_PATH}test43.err
echo -n $? > ${LOG_PATH}test43.!!!

# test44: Wrong Format3; Expected output: test44.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err14.fmt > ${LOCAL_OUT_PATH2}test44.out 2> ${LOG_PATH}test44.err
echo -n $? > ${LOG_PATH}test44.!!!

# test45: Wrong Format3; Expected output: test45.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err15.fmt > ${LOCAL_OUT_PATH2}test45.out 2> ${LOG_PATH}test45.err
echo -n $? > ${LOG_PATH}test45.!!!

# test46: Wrong Format3; Expected output: test46.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err16.fmt > ${LOCAL_OUT_PATH2}test46.out 2> ${LOG_PATH}test46.err
echo -n $? > ${LOG_PATH}test46.!!!

# test47: Wrong Format3; Expected output: test47.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err17.fmt > ${LOCAL_OUT_PATH2}test47.out 2> ${LOG_PATH}test47.err
echo -n $? > ${LOG_PATH}test47.!!!

# test48: Wrong Format3; Expected output: test48.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err18.fmt > ${LOCAL_OUT_PATH2}test48.out 2> ${LOG_PATH}test48.err
echo -n $? > ${LOG_PATH}test48.!!!

# test49: Wrong Format3; Expected output: test49.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err19.fmt > ${LOCAL_OUT_PATH2}test49.out 2> ${LOG_PATH}test49.err
echo -n $? > ${LOG_PATH}test49.!!!

# test50: Wrong Format3; Expected output: test50.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err20.fmt > ${LOCAL_OUT_PATH2}test50.out 2> ${LOG_PATH}test50.err
echo -n $? > ${LOG_PATH}test50.!!!

# test51: OK; Expected output: test51.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=ok21.fmt > ${LOCAL_OUT_PATH2}test51.out 2> ${LOG_PATH}test51.err
echo -n $? > ${LOG_PATH}test51.!!!

# test52: Wrong Format3; Expected output: test52.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err22.fmt > ${LOCAL_OUT_PATH2}test52.out 2> ${LOG_PATH}test52.err
echo -n $? > ${LOG_PATH}test52.!!!

# test53: Wrong; Expected output: test53.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err23.fmt > ${LOCAL_OUT_PATH2}test53.out 2> ${LOG_PATH}test53.err
echo -n $? > ${LOG_PATH}test53.!!!

# test54: OK; Expected output: test54.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=ok1.fmt > ${LOCAL_OUT_PATH2}test54.out 2> ${LOG_PATH}test54.err
echo -n $? > ${LOG_PATH}test54.!!!

# test55: Wrong; Expected output: test55.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err24.fmt > ${LOCAL_OUT_PATH2}test55.out 2> ${LOG_PATH}test55.err
echo -n $? > ${LOG_PATH}test55.!!!

# test56: Wrong; Expected output: test56.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err25.fmt > ${LOCAL_OUT_PATH2}test56.out 2> ${LOG_PATH}test56.err
echo -n $? > ${LOG_PATH}test56.!!!

# test57: Wrong; Expected output: test57.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err26.fmt > ${LOCAL_OUT_PATH2}test57.out 2> ${LOG_PATH}test57.err
echo -n $? > ${LOG_PATH}test57.!!!

# test58: Wrong; Expected output: test58.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err27.fmt > ${LOCAL_OUT_PATH2}test58.out 2> ${LOG_PATH}test58.err
echo -n $? > ${LOG_PATH}test58.!!!

# test59: Wrong; Expected output: test59.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err28.fmt > ${LOCAL_OUT_PATH2}test59.out 2> ${LOG_PATH}test59.err
echo -n $? > ${LOG_PATH}test59.!!!

# test60: Wrong; Expected output: test60.out; Expected return code: 4
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}example.in  --format=err29.fmt > ${LOCAL_OUT_PATH2}test60.out 2> ${LOG_PATH}test60.err
echo -n $? > ${LOG_PATH}test60.!!!

# test61: OK; Expected output: test61.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}basic-parameter.in  --format=ok2.fmt > ${LOCAL_OUT_PATH2}test61.out 2> ${LOG_PATH}test61.err
echo -n $? > ${LOG_PATH}test61.!!!

# test62: OK; Expected output: test62.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}basic-parameter.in  --format=linefeeds.fmt > ${LOCAL_OUT_PATH2}test62.out 2> ${LOG_PATH}test62.err
echo -n $? > ${LOG_PATH}test62.!!!

for i in `ls ./moj-out/ | grep -e '.*\.[^e]'`
	do
		echo "Test: $i"
		printf "\n"
		diff -a ./moj-out/"$i" ./ref-out/"$i"
		if [ $? -ne 0 ]; then
			((COUNT++))
		fi	
		printf "\n**********************************************************************\n"
	done
echo "Nepreslo: $COUNT testov";

ls
find moj-out/

exit $COUNT
