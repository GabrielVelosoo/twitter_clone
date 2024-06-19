<?php

    namespace App\Models;

    use MF\Model\Model;

    class Tweet extends Model {

        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        public function __get($attr){

            return $this->$attr;

        }

        public function __set($attr, $value){

            $this->$attr = $value;

        }

        //salvar
        public function salvar(){

            $query = "insert into tweets(id_usuario, tweet) values (:id_usuario, :tweet)";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));

            $stmt->execute();

            return $this;

        }

        //recuperar
        public function getAll(){

            $query = "
                select 
                    tweets.id, tweets.id_usuario, usuarios.nome, tweets.tweet, DATE_FORMAT(tweets.data, '%d/%m/%Y %H:%i') as data 
                from 
                    tweets left join usuarios on (tweets.id_usuario = usuarios.id) 
                where 
                    tweets.id_usuario = :id_usuario or tweets.id_usuario in (select id_usuario_seguindo 
                    from 
                        usuarios_seguidores 
                    where 
                        id_usuario = :id_usuario) 
                order by 
                    tweets.data desc
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        }

        public function delete(){

            $query = "delete from tweets where id = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id', $this->__get('id'));

            $stmt->execute();

            return true;

        }
        
        //recuperar com paginação
        public function getPorPagina($limit, $offset){

            $query = "
                select 
                    tweets.id, tweets.id_usuario, usuarios.nome, tweets.tweet, DATE_FORMAT(tweets.data, '%d/%m/%Y %H:%i') as data 
                from 
                    tweets left join usuarios on (tweets.id_usuario = usuarios.id) 
                where 
                    tweets.id_usuario = :id_usuario or tweets.id_usuario in (select id_usuario_seguindo 
                    from 
                        usuarios_seguidores 
                    where 
                        id_usuario = :id_usuario) 
                order by 
                    tweets.data desc
                limit
                    $limit
                offset
                    $offset
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        }

        //recuperar total de tweets
        public function getTotalRegistros(){

            $query = "
                select 
                    count(*) as total
                from 
                    tweets left join usuarios on (tweets.id_usuario = usuarios.id) 
                where 
                    tweets.id_usuario = :id_usuario or tweets.id_usuario in (select id_usuario_seguindo 
                    from usuarios_seguidores where id_usuario = :id_usuario) 
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));

            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }

    }