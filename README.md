Graphit - PHP GraphQL Framework
===============================

Graphit adalah 'framework' khusus untuk membuat aplikasi [graphql](http://graphql.org/).
Tidak seperti framework PHP pada umumnya yang menyajikan _RESTful routing_, middleware, ORM, dsb. 
Graphit hanya memiliki 1 fungsi utama, yaitu mengeksekusi graphql query (gql).
Untuk mengeksekusi gql tersebut, Graphit menggunakan library [webonyx/graphql-php](https://github.com/webonyx/graphql-php).

Seperti framework pada umumnya, Graphit dibuat bertujuan untuk:

1. Easy to build.
2. Easy to maintain.

Untuk tujuan tersebut, Graphit menghadirkan fitur-fitur sebagai berikut:

* Mempermudah definisi _schema_ dengan graphql IDL.
* ~~Memaksa~~ Membantu menciptakan struktur direktori yang lebih mudah dipahami.
* _AST caching_ untuk mempercepat performa aplikasi.
* Depencency Injection (DI) Container.
* Integrasi [GraphiQL](https://github.com/graphql/graphiql).
* Mudah di integrasikan ke beberapa framework PHP populer.

## Instalasi

Untuk menginstall Graphit, silahkan buat direktori aplikasi baru. 
Kemudian jalankan perintah composer dibawah ini:

```
composer require emsifa/graphit:dev-master
```

## Setup

> On progress

## Konfigurasi

> On progress

## TODOS

- [ ] Automatic build schema from `.graphql` file
  - [x] Register Queries
  - [x] Register Mutations
  - [x] Register Types
  - [x] Register Enums
  - [x] Register Inputs
  - [x] Register Interfaces
  - [ ] Register Unions
- [x] Execute gql from HTTP
- [x] GraphiQL integration
- [x] AST caching
- [x] Upload file support following [this specs](https://github.com/jaydenseric/graphql-multipart-request-spec)
- [ ] Framework Integrations
  - [ ] Laravel
  - [ ] Slim
  - [ ] Silex
  - [ ] Yii2
  - [ ] Rakit