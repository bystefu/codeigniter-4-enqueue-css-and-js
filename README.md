# codeigniter-4-enqueue-css-and-js
Library for enqueue CSS and JS in CI4, sort by parents and childrens, set dependencies and order by position.Load in header or footer.

First, you must to put "Enqueue.php" in your "Libraries" directory from CI4, CI/app/Libraries
Second, you must to load this library in CI/app/Config/Autoload.php with classmap,or whatever you want :  public $classmap = ['Enqueue'=>APPPATH.'/Libraries/Enqueue.php'];


Call this : 

use App\Libraries\Enqueue;
$css = new Enqueue();

Create a array : 

        $arr = [
            'Bootstrap' => ['url' => 'bootstrap.css', 'dependencies' => '', 'position' => 4, 'where' => 'header'],
            'Buttons' => ['url' => 'buttons.css', 'dependencies' => 'Bulma', 'position' => 3, 'where' => 'footer'],
            'Bulma' => ['url' => 'bulma.css', 'dependencies' => '', 'position' => 2, 'where' => 'footer'],
            'Love' => ['url' => 'love.css', 'dependencies' => 'Bootstrap', 'position' => 8, 'where' => 'header'],
        ];
        
And call : 

        echo $css->load(
            $arr,
            'position' / *order by position or other key (like year)*/,
            'all' /*default : all (optional 'parents' or 'childrens') */,
            'everywhere' /* default : 'everywhere' (optional 'header' or 'footer' */
        );
        
For more, view example.php
I don't coding from many years ago, i started couple days ago, i'm sorry if is ugly or method is old.But i will be glad if is usefull for you.
