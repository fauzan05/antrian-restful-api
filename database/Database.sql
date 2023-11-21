show databases;

use antrian;

show tables;
show create table users;
select * from migrations;
select * from users;
select * from services;
select * from personal_access_tokens;
select * from password_reset_tokens;
select * from counters;
select * from failed_jobs;
select * from queues;

delete from migrations where id = 23;
drop table services;
drop table failed_jobs;
drop table migrations;


