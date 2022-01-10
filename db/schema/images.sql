create table images
(
	id         int auto_increment
		primary key,
	recipe_id  int          not null,
	created_at timestamp    not null,
	changed_at timestamp    null on update CURRENT_TIMESTAMP,
	name       varchar(100) not null,
	label      varchar(255) null,
	width      int unsigned not null,
	height     int unsigned not null,
	constraint images_ibfk_2
		foreign key (recipe_id) references recipes (id)
			on update cascade on delete cascade
);

create index recipe_id
	on images (recipe_id);
