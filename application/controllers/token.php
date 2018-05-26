<?php
ini_set('memory_limit', '-1'); 
ini_set('max_execution_time', 0);
use Skyeng\Lemmatizer;
use Skyeng\Lemma;
include(APPPATH.'controllers/stemmer.php');

class Token extends CI_Controller {
	//tokenizer function split text into words and convert all letters to small letters
	public function tokenizer($text=null){
		/*$result_capital = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$)|([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d)))/', $text,-1, PREG_SPLIT_NO_EMPTY);*/
		$result_capital = explode(" ",$text);
		$result_small=array();
		for ($i=0;$i<sizeof($result_capital);$i++) {
			$result_capital[$i] = str_replace(array(',', '.'), '', $result_capital[$i]);
			$result_small[$i]=strtolower($result_capital[$i]);
		}
		return $result_small;
	}

	//removeStopWords function 
	public function removeStopWords($input){
		$StopWords = array('a','about','above','after','again','against','all','am','an','and','any','are','aren\'t','as','at','be','because','been','before','being','below','between','both','but','by','can\'t','cannot','could','couldn\'t','did','didn\'t','do','does','doesn\'t','doing','don\'t','down','during','each','few','for','from','further','had','hadn\'t','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','he\'s','her','here','here\'s','hers','herself','him','himself','his','how','how\'s','i','i\'d','i\'ll','i\'m','i\'ve','if','in','into','is','isn\'t','it','it\'s','its','itself','let\'s','me','more','most','mustn\'t','my','myself','no','nor','not','of','off','on','once','only','or','other','ought','our','ours','ourselves','out','over','own','same','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','so','some','such','than','that','that\'s','the','their','theirs','them','themselves','then','there','there\'s','these','they','they\'d','they\'ll','they\'re','they\'ve','this','those','through','to','too','under','until','up','very','was','wasn\'t','we','we\'d','we\'ll','we\'re','we\'ve','were','weren\'t','what','what\'s','when','when\'s','where','where\'s','which','while','who','who\'s','whom','why','why\'s','with','won\'t','would','wouldn\'t','you','you\'d','you\'ll','you\'re','you\'ve','your','yours','yourself','yourselves');
		return preg_replace('/\b('.implode('|',$StopWords).')\b/','',$input);
	}


	function getIndex() {
	    //this statement for lemmatize
	    require_once APPPATH . "/vendor/autoload.php";
	    $s = new Stemmer();
		$lemmatizer = new Lemmatizer();
		
		//get all corpus file
	    $this->load->model('doc_model');
		$files=$this->doc_model->get_docs();

		//Dictionary structure is an array(term=>array('df'=>int,'posting'=>array(docID=>array('tf'=>int))))
	    $dictionary = array();
	    //docCount= array(docID=>number of its words after tokenize,remove stop word, stemming, lemmatization)
	    $docCount = array();
	    //documents=array(docID=>content of this document)
	    $documents=array();

	    //handles files(tokenize,remove stop word, stemming, lemmatization)
	    foreach($files as $value) {
	    	//tokenization
	    	$terms=null;
			$content=file_get_contents($value->docPath);
			$tokens=$this->tokenizer($content);
			for ($i=0; $i <sizeof($tokens) ; $i++) { 
				$tokens[$i]=$this->doc_model->check_lookup($tokens[$i]);
			}
			$removedStopWords=$this->removeStopWords($tokens);
			$fileStemString=$s->stem_list($removedStopWords);
			foreach ($fileStemString as $key => $value1) {
				$lemma=$lemmatizer->getOnlyLemmas($value1);
				$terms[$key] = $lemma[0];
			}
			//here we go over words of document
			foreach($terms as $key=>$term) {
	        	if (is_array ($term)) {
	        		$term=array_values($term)[0];
	        	}
	        	$term=$this->doc_model->check_lookup($term);
				$docID=$value->docID;
		
				//from here we fill the index
	    		//new term (first time in dictionary)
	            if(!isset($dictionary[$term])) {
	                    $dictionary[$term] = array('postings' => array());
	            }
	            //existing term but appear in new document
	            if(!isset($dictionary[$term]['postings'][$docID])) {
	                    $dictionary[$term]['postings'][$docID] = array('tf' => 0);
	            }
	            //existing term appear in existing previous document so we increment the tf
	            $dictionary[$term]['postings'][$docID]['tf']++;
	        }
	    }

	    $index=array('dictionary' => $dictionary);
	    $this->doc_model->index_docs($index);
	    $this->doc_model->convert_to_indexing($files);
	    echo "ok";
	}

	//execute tfidf rule for a document
	function getTfidf($docID) {
	    //count of all documents in the corpus
	    $this->load->model('doc_model');
	    $docCount = $this->doc_model->corpus_docs_count();
	    $doc=$this->doc_model->get_doc_terms($docID);
	    $d=array();
	    foreach ($doc as $value) {	
	    ////////////////////////////////attention////////////////////        
	    	$d[$value->termID]=$value->tf * log(3 / $this->doc_model->get_df($value->termID), 2);
	    }
	    //var_dump($d);
	    return $d;
	}

	//execute tfidf rule for a query
	function queryTfidf($query="gold silver truck") {
		$this->load->model('doc_model');
		$s = new Stemmer();
		$lemmatizer = new Lemmatizer();
		$tokens=$this->tokenizer($query);
		$removedStopWords=$this->removeStopWords($tokens);
		$fileStemString=$s->stem_list($removedStopWords);
		$terms=array();
		foreach ($fileStemString as $key =>$value1) {
			$lemma=$lemmatizer->getOnlyLemmas($value1);
			$terms[$key] = $lemma[0];
			if (is_array ($terms[$key])) {
				$terms[$key]=array_values($terms[$key])[0];
			}
		}
		$docCount = $this->doc_model->corpus_docs_count();
		$max=$this->maxFreq($terms);
		$q=array();
		$values = array_count_values($terms);
		foreach ($values as $term => $frq) {
			$value=$this->doc_model->get_term_id($term);
			/////////////////////////attention////////////////////////////////
			$q[$value->termID]=($frq/$max)*log(3 / $this->doc_model->get_df($value->termID), 2);
		}
		//var_dump($q);
		return $q;
	}

	//calculate the length of document or query (vertor length)
	function length($doc) {
		$total=0;
	    foreach($doc as $entry) {
	            $total += $entry*$entry;
	    }
	    $total = sqrt($total);
	    return $total;
	}

	//cos similarity function (this function calculate the distance between document and query)
	function cosineSim($query,$doc) {
	    $result = 0;
	    $numerator=0;
	    foreach($query as $term_id => $tfidf) {
	    	if (array_key_exists($term_id,$doc)){
	            $numerator += $tfidf * $doc[$term_id];
	    	}
	    }
	    $denominator =$this->length($query)*$this->length($doc);
	    if ($denominator != 0) {
	    	$result=$numerator/$denominator;
	    }
	    return $result;
	}

	//this function get the biggest frequent in the vector
	public function maxFreq($array)
	{
		$values = array_count_values($array);
		arsort($values);
		$max=array_values($values);
		return $max[0];
	}

	//execution function
	public function execute (){
		$query=$this->queryTfidf();
		for ($docID=0; $docID <4 ; $docID++) { 
		 	$doc=$this->getTfidf($docID);
			$matchDocs[$docID]=$this->cosineSim($query,$doc);
		 } 
		arsort($matchDocs); // sort matching files from high to low

		var_dump($matchDocs);
	}

	function Index() {
	    //this statement for lemmatize
	    require_once APPPATH . "/vendor/autoload.php";
	    $s = new Stemmer();
		$lemmatizer = new Lemmatizer();
		
		//get all corpus file
	    $this->load->model('doc_model');
		//$files=$this->doc_model->get_docs();
		$files = array(
                1 => 'shipment of gold damaged in a fire $',
                2 => 'delivery of silver arrived in a silver truck cad',
                3 => 'on Sat shipment of gold arrived in a truck '
        );

		//Dictionary structure is an array(term=>array('df'=>int,'posting'=>array(docID=>array('tf'=>int))))
	    $dictionary = array();
	    //docCount= array(docID=>number of its words after tokenize,remove stop word, stemming, lemmatization)
	    $docCount = array();
	    //documents=array(docID=>content of this document)
	    $documents=array();

	    //handles files(tokenize,remove stop word, stemming, lemmatization)
	    foreach($files as $key1=>$value) {
	    	//tokenization
	    	$terms=null;
			//$content=file_get_contents($value->docPath);
			$tokens=$this->tokenizer($value);
			for ($i=0; $i <sizeof($tokens) ; $i++) { 
				$tokens[$i]=$this->doc_model->check_lookup($tokens[$i]);
			}
			$removedStopWords=$this->removeStopWords($tokens);

			$fileStemString=$s->stem_list($removedStopWords);
			
			foreach ($fileStemString as $key => $value1) {
				$lemma=$lemmatizer->getOnlyLemmas($value1);
				$terms[$key] = $lemma[0];
			}
			//here we go over words of document
			foreach($terms as $key=>$term) {
	        	if (is_array ($term)) {
	        		$term=array_values($term)[0];
	        	}
	        	
				$docID=$key1;
		        $documents[$docID]=$terms;
		        $docCount[$docID] = count($terms);
		
				//from here we fill the index
	    		//new term (first time in dictionary)
	            if(!isset($dictionary[$term])) {
	                    $dictionary[$term] = array('postings' => array());
	            }
	            //existing term but appear in new document
	            if(!isset($dictionary[$term]['postings'][$docID])) {
	                    $dictionary[$term]['postings'][$docID] = array('tf' => 0);
	            }
	            //existing term appear in existing previous document so we increment the tf
	            $dictionary[$term]['postings'][$docID]['tf']++;
	        }
	    }

	    $index=array('documents'=>$documents,'docCount' => $docCount, 'dictionary' => $dictionary);
	    $this->doc_model->index_docs($index);
	    //$this->doc_model->convert_to_indexing($files);
	    echo "ok";
	}

	public function test()
	{
		$this->load->model('doc_model');
		$term=$this->doc_model->check_lookup('$');
		echo $term;
	}
}