## Polder wordpress website

A website for polder initiative from uva.nl.

## Tech stack

Having the knowledge of this is necessary to spin up the development environment.

- Docker
- Wordpress
- MySQL
- Bootstrap

## Development

```sh
docker compose up -d
```

This will spin up docker, populate all the data from `data/dump.sql`. You can then go to the browser and see the website by visiting

[http://localhost:8000](http://localhost:8000)


You may login to the admin by visiting [http://localhost:8000/wp-admin](http://localhost:8000/wp-admin) (username/password: `admin/admin` )

## Theme

We are using [bootstrap](https://getbootstrap.com/) as our primary design system. We use a child theme of [picostrap5](https://picostrap.com/) which is a bootstrap starter theme. We have couple of custom content types of our own (News, Event, Publication, Lab) which are customised in the child theme.

## Plugins

We use couple of must-use (mu) plugins which you can find within `mu-plugins` directory. These are loaded automatically and are necessary for the working of the website.

## Notes

1. To take the db dump

  ```sh
  docker exec polder-db-1 mysqldump -u wordpress --password=wordpress wordpress > data/dump.sql
  ```
