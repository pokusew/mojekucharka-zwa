create table categories
(
	id        int auto_increment primary key,
	parent_id int         null,
	name      varchar(30) not null,
	constraint categories_ibfk_1
		foreign key (parent_id) references categories (id)
			on update cascade on delete cascade
);

create index parent_id
	on categories (parent_id);
