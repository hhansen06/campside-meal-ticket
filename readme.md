### 



## Infos 
### Create user 
- docker compose exec -it database mysql -uroot -pchangeme -Dzeltlager -e "Insert into user (email, password, enabled,time_created) VALUES (\"admin\", SHA1(\"admin\") ,1,0);"
