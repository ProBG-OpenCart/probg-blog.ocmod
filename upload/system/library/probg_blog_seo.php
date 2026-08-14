<?php
class ProbgBlogSeo {
    private $db;
    public function __construct($db = null) { $this->db = $db; }
    public function slugify($text) {
        $map=array('А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ж'=>'Zh','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'Ts','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sht','Ъ'=>'A','Ь'=>'','Ю'=>'Yu','Я'=>'Ya','а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sht','ъ'=>'a','ь'=>'','ю'=>'yu','я'=>'ya');
        $text=html_entity_decode(strip_tags((string)$text),ENT_QUOTES,'UTF-8');
        $text=strtr($text,$map);
        if(function_exists('iconv')){$ascii=@iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$text);if($ascii!==false)$text=$ascii;}
        $text=strtolower($text);
        $text=preg_replace('/[^a-z0-9]+/','-',$text);
        $text=trim($text,'-');
        return $text!==''?$text:'blog';
    }
    public function uniqueKeyword($keyword,$language_id,$query='',$store_id=0) {
        if(!$this->db)return $keyword;
        $base=substr($keyword,0,240);$candidate=$base;$suffix=2;
        while(true){
            $sql="SELECT seo_url_id FROM `".DB_PREFIX."seo_url` WHERE store_id='".(int)$store_id."' AND language_id='".(int)$language_id."' AND keyword='".$this->db->escape($candidate)."'";
            if($query!=='')$sql.=" AND query!='".$this->db->escape($query)."'";
            $exists=$this->db->query($sql);
            if(!$exists->num_rows)return $candidate;
            $tail='-'.$suffix++;$candidate=substr($base,0,255-strlen($tail)).$tail;
        }
    }
}
