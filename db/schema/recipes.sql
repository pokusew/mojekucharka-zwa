create table recipes
(
	id             int auto_increment
		primary key,
	user_id        int               not null,
	created_at     timestamp         not null,
	changed_at     timestamp         null on update CURRENT_TIMESTAMP,
	deleted_at     timestamp         null,
	public         tinyint default 0 not null,
	name           varchar(255)      not null,
	category_id    int               not null,
	main_image_id  int               null,
	ingredients    text              null,
	instructions   text              null,
	private_rating tinyint           null,
	constraint recipes_ibfk_4
		foreign key (user_id) references users (id)
			on update cascade on delete cascade,
	constraint recipes_ibfk_5
		foreign key (category_id) references categories (id)
			on update cascade,
	constraint recipes_ibfk_7
		foreign key (main_image_id) references images (id)
			on update cascade on delete set null
);

create index category_id
	on recipes (category_id);

create index main_image_id
	on recipes (main_image_id);

create index public
	on recipes (public);

create index user_id
	on recipes (user_id);

