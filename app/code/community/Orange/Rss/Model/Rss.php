<?php
/*
author: SB
support: mage.ext@gmail.com
*/
class Orange_Rss_Model_Rss extends Mage_Core_Model_Abstract
{
	function getStoreData($item)
	{
		return Mage::getStoreConfig('orange_rss/settings/' . $item);
	}
	public function getTitle()
	{
		return $this->getStoreData('title');
	}
	public function isActive()
	{
		return $this->getStoreData('enable');
	}
	public function showDate()
	{
		return $this->getStoreData('show_date');
	}
	public function showContent()
	{
		return $this->getStoreData('show_content');
	}
	public function data()
	{
		$rssLink = $this->getStoreData('rss_link');
                $rssLink="http://www.celebrationtemplates.com/blog/feed/rss/";
      
		if(file_get_contents($rssLink)) {
			$feed = Zend_Feed_Reader::import($rssLink);
                    //$feed = Zend_Feed::import($rssLink);
			
			$data = array(
				'title'        => $feed->getTitle(),
				'link'         => $feed->getLink(),
				'dateModified' => $feed->getDateModified(),
				'description'  => $feed->getDescription(),
				'language'     => $feed->getLanguage(),
				'entries'      => array(),
			);
	 
			$i = 0;
			foreach ($feed as $entry) {
				++$i;
				$edata = array(
					'title'        => $entry->getTitle(),
					'description'  => $entry->getDescription(),
					'dateModified' => $entry->getDateModified(),
					'authors'      => $entry->getAuthors(),
					'link'         => $entry->getLink(),
					'content'      => $entry->getContent()
				);
				$data['entries'][] = $edata;
				if($i == $this->getStoreData('showposts'))
					break;
			}
			return $data;
		} else {
			return false;
		}
	}
}
