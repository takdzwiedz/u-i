<?php


interface Depot
{
    public function store(int $amount, float $price): void;

    public function pull(int $amount): float;
}

abstract class AbstractDepot
{
    protected $bricksOnStock;
    private $amount;

    public function __construct()
    {
        $this->bricksOnStock = [];
        $this->amount = 0;
    }

    public function getBricksOnStock()
    {
        return $this->bricksOnStock;
    }

    public function setBricksOnStock(array $bricksOnStock): void
    {
        $this->bricksOnStock = $bricksOnStock;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function showBricksOnStock()
    {
        echo "<pre>";
        print_r($this->getBricksOnStock());
    }

}

final class BuildingDepot extends AbstractDepot implements Depot
{

    public function store(int $amount, float $price): void
    {
        $bricksOnStock = $this->getBricksOnStock();
        $bricksOnStock[] = array(
            "amount" => $amount,
            "price" => $price
        );
        $this->setBricksOnStock($bricksOnStock);
    }

    public function pull(int $amount): float
    {
        $this->setAmount($amount);
        $totalAmountOfBricks = $this->countBricksOnStock();

        try {
            if ($amount > $totalAmountOfBricks) {
                $missing = $amount - $totalAmountOfBricks;
                throw new Exception("Not enough bricks on stock! Missing amount of bricks: $missing.");
            } else {
                $price = 0;
                while ($amount > 0) {

                    $data = $this->updateSet($amount, $price);
                    $amount = $data["newAmount"];
                    $price = $data["partPrice"];
                }
                return $price;
            }
        } catch (Exception $e) {
            echo "<b>Exception</b>: " . $e->getMessage();
            exit();
        }
    }

    public function countBricksOnStock()
    {
        $totalAmountOfBricks = 0;
        foreach ($this->getBricksOnStock() as $set) {
            $totalAmountOfBricks += $set["amount"];
        }
        return $totalAmountOfBricks;
    }

    public function updateSet(int $amount, float $price): array
    {
        $bricksInSet = $this->getBricksOnStock()[0]["amount"];
        $priceInSet = $this->getBricksOnStock()[0]["price"];

        if ($bricksInSet > $amount) {
            $newBricksQuantityInSet = $bricksInSet - $amount;
            $newAmount = 0;
            $price = $price + $amount * $priceInSet;
            $bricksOnStock = $this->getBricksOnStock();
            $bricksOnStock[0] = array(
                "amount" => $newBricksQuantityInSet,
                "price" => $priceInSet
            );
            $this->setBricksOnStock($bricksOnStock);
        } else {
            $newAmount = $amount - $bricksInSet;
            $price = $price + $bricksInSet * $priceInSet;
            unset($this->bricksOnStock[0]);
            $this->setBricksOnStock(array_values($this->getBricksOnStock()));
        }

        $data = array(
            "newAmount" => $newAmount,
            "partPrice" => $price
        );

        return $data;
    }

}

$obj = new BuildingDepot();
$obj->store(1000, 2.5);
$obj->pull(700);
$obj->store(200, 2.4);
$obj->store(1000, 2.3);
$obj->pull(1000);
$obj->pull(501);