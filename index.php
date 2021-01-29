<?php

// Базовый класс робота
class Robot{
     public $weight, $speed, $height;
}

// Создаем определенного робота
class Robot1 extends Robot{
    public $weight = 50, $speed = 1, $height = 3;
}

// Реализация "модели" для хранения массива с роботами и выполнения операций над ним
class RobotDB{
    // Массив с роботами(хранит ключ и значение, где ключ это имя класса, а значение - его объект)
    protected $data;

    // Добавление робота в массив
    protected function addObject($name, $object){
        $this->data[$name] = $object;
    }

    // Получение робота из массива по имени
    protected function getObject($name){
        return $this->data[$name];
    }

    // Проверка на наличие робота в массиве
    protected function checkObject($name){
        if(array_key_exists($name, $this->data)){
            return true;
        } else {
            return false;
        }
    }
}

// Реализация класса Factory Robot
class FactoryRobot extends RobotDB {
    // Возвращает массив объектов
    /*
     * @return false | array
     */
    function __call($name, array $params){
        // Получаем имя объекта
        $objectName = mb_strcut($name, 6);

        // Если объект присутствует и задано колво
        if($this->checkObject($objectName) && isset($params[0])){
            //Инициализируем массив
            $arr = array($this->getObject($objectName));
            // Дозаполняем массив
            for($i = 1; $i <= $params[0]; $i++){
                $arr[] = $this->getObject($objectName);
            }
            // Возвращаем массив
            return $arr;
        } else {
            return false;
        }
    }

    // Реализация addType
    /*
     * @param Robot
     * @return false or nothing
     */
    function addType($object){
        // Reflection API
        $class = new ReflectionObject($object);

        // Если объект отсутствует у нас в RobotDB
        if(!$this->checkObject($class->getName())){
            // Добавляем объект
            $this->addObject($class->getName(), $object);
        } else {
            return false;
        }
    }
}

// Реализация MergeRobot
class MergeRobot {
    // Тут храним всех роботов, аналогично с FactoryRobot
    protected $data;

    // Инициализируем первого робота в массиве, характеристики которого, являются характеристиками всего MergeRobot
    function __construct()
    {
        $r = new Robot();
        $r->height = 0;
        $r->speed = 9999999;
        $r->weight = 0;
        $this->data[1] = $r;
    }

    //Реализация функций для получения параметров
    function getWeight(){
        return $this->data[1]->weight;
    }

    function getHeight(){
        return $this->data[1]->height;
    }

    function getSpeed(){
        return $this->data[1]->speed;
    }

    // Добавление робота в MergeRobot
    /*
     * @param Robot or array with Robot
     */
    function addRobot($object){
        if (is_array($object)){
            foreach ($object as $val){
                $this->updateMainRobot($val);
            }
        } else {
            $this->updateMainRobot($object);
        }
    }

    // Добавление и обновление характеристик MergeRobot
    /*
     * @param Robot
     */
    protected function updateMainRobot($robot){
        //Добавляем в MergeRobot
        array_push($this->data, $robot);
        //Апдейтим все характеристики
        $this->data[1]->height += $robot->height;
        $this->data[1]->weight += $robot->weight;
        $this->data[1]->speed = min($this->data[1]->speed, $robot->speed);
    }
}





////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$f = new FactoryRobot();
//$f->addType(new Robot2());
//$f->createRobot2(5);

$a = new MergeRobot();
$a->addRobot(new Robot1());
$a->addRobot(new Robot1());

$f->addType($a);
$res = reset($f->createMergeRobot(1));
print_r($res);

echo $res->getSpeed();
echo $res->getWeight();
echo $res->getHeight();
