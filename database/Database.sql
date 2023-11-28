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
select * from queues order by number asc;
delete from counters where is_active = 1;
delete from counters where user_id = 4;
delete from queues where service_id = 1;

select counters.name, services.name from counters inner join services on counters.id = services.counter_id;

delete from migrations where id = 23;
drop table services;
drop table counters;
drop table queues;
drop table users;
drop table failed_jobs;
drop table migrations;
drop table password_reset_tokens;
drop table personal_access_tokens;

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