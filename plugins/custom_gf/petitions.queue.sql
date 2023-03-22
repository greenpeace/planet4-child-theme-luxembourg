

create table queue (
id bigint unsigned not null auto_increment,
exported tinyint unsigned default 0,
api_reference varchar(50),
email varchar(255),
json_data text,
journey_id_new varchar(50),
journey_id_existing varchar(50),
nro char(2),
lang varchar(30),
campaign_id varchar(20),
transformer varchar(40),
optins text,
signup_date date,
signup_time time,
form_id int unsigned,
form_name varchar(255),
entry_id bigint unsigned,
source_url text,
user_agent text,
user_ip varchar(45),
primary key (id),
unique (api_reference),
key(nro),
key (email),
key (form_id),
key(campaign_id)
);

