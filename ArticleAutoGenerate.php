<?php
namespace articleGenerate;

class ArticleAutoGenerate
{
    // 名人名言
    protected $quotes = [];
    // 前面垫话
    protected $before = [];
    // 后面垫话
    protected $last = [];
    // 废话
    protected $nonsense = [];
    // 重复度
    protected $repeat = 0;
    // 标题
    protected $title;
    // 设置
    protected $option = [
        'word_count'=>6000,
    ];
        // 废话池
    protected static $nonsense_pool;
    // 名人名言池
    protected static $quotes_pool;

    public function __construct($title, $option=[]){
        // 设置文章标题
        $this->title = $title;
        // 设置重复度
        $this->repeat = empty($option['repeat']) ? $option['repeat'] :  2;
        // 设置
        $this->option =array_merge($this->option,$option);
        // 填充名人名言池
        self::$nonsense_pool = $this->pool_rand($this->nonsense);
        // 填充废话池
        self::$quotes_pool = $this->pool_rand($this->quotes);
    }

    /**
     * 生成文章
     */
    public function generate()
    {
        $content = $this->title;
        $content .= "\n";
        $tmp = "";
        while (mb_strlen($tmp) < $this->option['word_count']){
            $branch = rand(0,100);
            if($branch < 5){
                $tmp .= $this->next_paragraph();
            }elseif ($branch < 20){
                $tmp .= $this->get_quotes();
            }else{
                $tmp .= array_shift(self::$nonsence_pool);
            }
            $tmp = str_replace("x",$this->title,$tmp);
            $content .= $tmp;
            $content .= "\n";
        }
        return $content;
    }

    /**
     * 写文章
     * @param $filename
     * @param $path
     */
    public function write($filename,$path=""){
        $file_name = empty($filename) ? $this->title : $filename;
        $file_path = $path. '\\'.$file_name;
        if(!is_dir($path)){
            @mkdir($path);
        }
        $file = fopen($file_path,'w');
        fwrite($file,$this->generate());
        fclose($file);
    }

    /**
     * 获取名人名言句子
     */
    private function get_quotes()
    {
        // 名人名言句子
        $sentence = array_shift(self::$quotes_pool);
        // 替换名人名言前面垫话
        $sentence .= str_replace("a", array_rand($this->before),$sentence);
        // 替换名人名言后面垫话
        $sentence .= str_replace("b", array_rand($this->last),$sentence);

        return $sentence;
    }

    /**
     * 打乱池的顺序
     * @param $array
     * @return array
     */
    private function pool_rand($array)
    {
        $pool = $this->array_multiplication($array);
        shuffle($pool);
        return $pool;
    }

    /**
     * 结束前面一段，开启新的段落
     * @return string
     */
    private function next_paragraph()
    {
        $paragraph = "。 ";
        $paragraph .= "\r\n";
        $paragraph .= "    ";
        return $paragraph;
    }

    /**
     * 数组相乘（实现池的重复度）
     * @param $array
     * @return array
     */
    private function array_multiplication($array)
    {
        $new_array = [];
        for ($i = 0;$i < $this->repeat; $i++){
            $new_array += $array;
        }
        return $new_array;
    }
}  
    