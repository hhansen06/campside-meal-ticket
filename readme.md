## Info

Barcode for participants are numeric and are prefixed with *
*12345 would be a valid participant.
Daily meal tickets start with E and are prefixed with * => *E12345 would be a valid daypass.

if check_korrekt_zeltdorf is 1, only participants from a valid campside are allowed.
changing the active campside, can be archived with K Cards.

*K111 will toggle the check_korrekt_zeltdorf setting.
*K110 will allow all 

*Kxxx will set the a campside to active. xxx = zeltdorf_id


### Create user 
- docker compose exec -it database mysql -uroot -pchangeme -Dzeltlager -e "Insert into user (email, password, enabled,time_created) VALUES (\"admin\", SHA1(\"admin\") ,1,0);"

### Dashboard
- Kirchen dashboard under http://SERVERURL/dashboard.php 

### rest like api
Check Barcode:
http://SERVERIP/ajax.php?str_class=kasse&str_function=check_barcode&barcode=*E12345




