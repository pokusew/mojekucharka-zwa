create table users
(
	id                                int auto_increment
		primary key,
	username                          varchar(30)   not null,
	name                              varchar(255)  null,
	email                             varchar(255)  null,
	email_verified_at                 timestamp     null,
	email_verified_from_ip            varbinary(16) null,
	email_verification_key            varchar(255)  null,
	email_verification_key_created_at timestamp     null,
	password                          varchar(255)  not null,
	password_changed_at               timestamp     null,
	password_changed_from_ip          varbinary(16) null,
	password_reset_key                varchar(255)  null,
	password_reset_key_created_at     timestamp     null,
	registered_at                     timestamp     not null,
	registered_from_ip                varbinary(16) not null,
	constraint email
		unique (email),
	constraint email_email_verification_unverified
		unique (email),
	constraint username
		unique (username)
);

create index email_verification_key
	on users (email_verification_key);

create index password_reset_key
	on users (password_reset_key);

