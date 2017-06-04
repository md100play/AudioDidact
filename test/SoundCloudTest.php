<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 6/2/2017
 * Time: 7:31 PM
 */


require_once __DIR__."/../header.php";
use PHPUnit\Framework\TestCase;
use AudioDidact\SupportedSites\SoundCloud;

/**
 * @covers AudioDidact\SupportedSites\SoundCloud
 * Class SoundCloudTest
 */
class SoundCloudTest extends TestCase{
	private $testUrl = "https://soundcloud.com/ravishouse/andy-gruhin-bring-me-down-ravi";

	public function testConstructor(){
		$sc = new SoundCloud($this->testUrl, false);
		$this->assertEquals($sc->getVideo()->getURL(), $this->testUrl);
		$this->assertFalse($sc->getVideo()->isIsVideo());
		$sc = new SoundCloud($this->testUrl, true);
		$this->assertEquals($sc->getVideo()->getURL(), $this->testUrl);
		$this->assertFalse($sc->getVideo()->isIsVideo());
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Soundcloud URL is invalid
	 */
	public function testNonSoundCloudURL(){
		$sc = new SoundCloud("http://youtube.com/watch?v=12345678911", false);
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Soundcloud URL is invalid
	 */
	public function testBadURL(){
		$sc = new SoundCloud("http://soundcloud.com/ravishouse/", false);
	}

	public function testDownload(){
		$sc = new SoundCloud($this->testUrl, false);
		$video = $sc->getVideo();

		// Cleanup in case a previous test run failed before deleting downlaoded files
		@unlink(__DIR__."/../".DOWNLOAD_PATH."/".$video->getFilename().$video->getFileExtension());
		@unlink(__DIR__."/../".DOWNLOAD_PATH."/".$video->getThumbnailFilename());

		if(!$sc->allDownloaded()){
			$sc->downloadVideo();
			$sc->downloadThumbnail();
			if(!$video->isIsVideo()){
				$sc->convert();
			}
		}

		$this->assertFileExists(__DIR__."/../".DOWNLOAD_PATH."/".$video->getFilename().$video->getFileExtension());
		$this->assertFileNotExists(__DIR__."/../".DOWNLOAD_PATH."/".$video->getFilename().".mp4");
		$this->assertFileExists(__DIR__."/../".DOWNLOAD_PATH."/".$video->getThumbnailFilename());
		$this->assertEquals(52, $video->getDuration());

		unlink(__DIR__."/../".DOWNLOAD_PATH."/".$video->getFilename().$video->getFileExtension());
		unlink(__DIR__."/../".DOWNLOAD_PATH."/".$video->getThumbnailFilename());
	}

}
