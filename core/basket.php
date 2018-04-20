<?php

    class Basket
    {
        public function set($key, $value)
        {
            $this->$key = $value;
        }



        public function get($key)
        {
            return $this->$key ?? null;
        }
    }
