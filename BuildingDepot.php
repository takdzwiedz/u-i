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

    private $price;

    private $sum;

    private $totalAmountOfBricks;

    public function __construct()
    {
        $this->bricksOnStock = [];
        $this->amount = 0;
        $this->price = 0.00;
        $this->sum = 0;
        $this->totalAmountOfBricks = 0;
    }

    public function getBricksOnStock()
    {
        return $this->bricksOnStock;
    }

    public function setBricksOnStock(array $bricksOnStock): void
    {
        $this->bricksOnStock = $bricksOnStock;
    }

    public function getSum(): int
    {
        return $this->sum;
    }

    public function setSum(int $sum): void
    {
        $this->sum = $sum;
    }

    public function showBricksOnStock()
    {
        echo "<pre>";
        print_r($this->getBricksOnStock());
    }

    public function getTotalAmountOfBricks(): int
    {
        return $this->totalAmountOfBricks;
    }

    public function setTotalAmountOfBricks($totalAmountOfBricks): void
    {
        $this->totalAmountOfBricks = $totalAmountOfBricks;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

}

final class BuildingDepot extends AbstractDepot implements Depot
{

    public function store(int $amount, float $price): void
    {
        $bricksOnStock = $this->getBricksOnStock();
        $bricksOnStock[] = array("amount" => $amount, "price" => $price);
        $this->setBricksOnStock($bricksOnStock);
    }

    public function setUpdate(int $amount, float $partPrice): array
    {
        $bricksQuantityInSet = $this->getBricksOnStock()[0]["amount"];
        $priceInSet = $this->getBricksOnStock()[0]["price"];

        if ($bricksQuantityInSet > $amount) {
            $newBricksQuantityInSet = $bricksQuantityInSet - $amount;
            $newAmount = 0;
            $partPrice = $partPrice + $amount * $priceInSet;
            $x = $this->getBricksOnStock();
            array("amount" => $newBricksQuantityInSet, "price" => $priceInSet);
            $x[0] = array("amount" => $newBricksQuantityInSet, "price" => $priceInSet);
            $this->setBricksOnStock($x);

        } else {
            $newAmount = $amount - $bricksQuantityInSet;
            $partPrice = $partPrice + $bricksQuantityInSet * $priceInSet;
            unset($this->bricksOnStock[0]);
            $x = $this->getBricksOnStock();
            $this->setBricksOnStock(array_values($x));
        }

        $data = array(
            "newAmount" => $newAmount,
            "partPrice" => $partPrice
        );

        return $data;
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
                    $data = $this->setUpdate($amount, $price);
                    $amount = $data["newAmount"];
                    $price = $data["partPrice"];
                }
                return $price;
            }
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage(); exit();
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
}

$obj = new BuildingDepot();
$obj->store(1000, 2.5);
echo $obj->pull(700);
$obj->store(200, 2.4);
$obj->store(1000, 2.3);
$obj->pull(1000);
echo $obj->pull(1000);