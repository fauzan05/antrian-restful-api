show databases;

use antrian;
create database antrian;

show tables;
show create table users;
select * from migrations;
select * from services;
select * from users;
select * from admin_settings;
select * from operational_hours;
show create table operational_hours;
select * from personal_access_tokens;
select * from websockets_statistics_entries;
select * from password_reset_tokens;
select * from counters;
select * from failed_jobs;
select * from operational_hours where days = 'Senin';
select * from queues order by registration_number AND poly_number asc;
delete from counters where is_active = 1;
delete from counters where user_id = 4;
delete from queues where service_id = 1;
select * from queues where counter_registration_id = 679 order by id desc limit 1;
select counters.name, services.name from counters inner join services on counters.id = services.counter_id;
select * from operational_hours where created_at>='13:00';
delete from migrations where id = 23;
drop table services;
drop table counters;
drop table queues;
drop table users;
drop table failed_jobs;
drop table migrations;
drop table password_reset_tokens;
drop table personal_access_tokens;
drop table websockets_statistics_entries;
drop table app_settings;
select * from services where name like '%layanan pendaftaran%';

update counters set service_id = null where id = 1;
update operational_hours SET open='08:00:00', close='10:00:00' WHERE id='2';
select * from operational_hours where close < '07:00:00';
update admin_settings set selected_video = null where id = 1;

select counters.name, queues.number as current_number from ((counters
inner join services on counters.service_id = services.id)
inner join queues on services.id = queues.service_id)
where counters.id = '5' AND queues.status IN ('called', 'skipped') order by number desc limit 1;

select queues.number, services.name as service_name, queues.status, counters.name as counters_name from ((services
inner join queues on services.id = queues.service_id)
inner join counters on counters.service_id = services.id)
where counters.name = 'Loket 1' order by number;

select * from counters where id = 21 and service_id = 22;
delete from queues where service_id = 1;
delete from queues where counter_id = 122;