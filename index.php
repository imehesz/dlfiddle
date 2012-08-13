<?php
	require_once 'slim/Slim/Slim.php';

	$app = new Slim(array(
		'log.enable' => false,
		'view' => new Slim_View() 
	));

	$app->get('/', function () use ($app) {
		$errors = array();

		if( !empty( $_GET ) && isset( $_GET['jsfiddlelink'] ) ) {
			if( filter_var( $_GET['jsfiddlelink'], FILTER_VALIDATE_URL ) === false ) {
				$errors[] = 'Tsk, the URL you entered does not seem to be valid :/';
			}

			if( strpos( $_GET['jsfiddlelink'], 'http://jsfiddle.net/' ) !== 0 ) {
				$errors[] = "Oh, noooos! You can only use the <a href='http://jsfiddle.net' target='_blank'>JSFiddle.net</a> site. Sorry!";
			}

			if( empty( $errors ) ) {
				if( isset( $_GET['type'] ) && $_GET['type'] == 'tar' ) {
					if( ! _processFile( $_GET['jsfiddlelink'], 'tar.gz' ) ) {
						$errors[] = 'Processing failed, please try again!';
					}
				} else {
					if( ! _processFile( $_GET['jsfiddlelink'], 'zip' ) ) {
						$errors[] = 'Processing failed, please try again!';
					}
				}
			}
		}
		return $app->render( 'home.php', array( 'errors' => $errors ) );
	});

	$app->run();

	function _processFile( $link, $type ) {
		$file_name = str_ireplace( "http","",(preg_replace("/[^A-Za-z0-9-]/", "", $link)));

		if( ! file_exists( "fiddles/$file_name . '.' . $type" ) ) {
			$source_url = str_ireplace( 'jsfiddle.net', 'fiddle.jshell.net', $link ) . ( substr( $link, -1 ) == '/' ? 'show' : '/show' );

			$path = './fiddles/' . $file_name . '.html';
			$stream = @fopen( $source_url, 'r');

			$fp = @fopen($path, 'w');
			fwrite( $fp, stream_get_contents( $stream ) );
			fclose($fp);

			// TODO hmmmmmmm
			// we create both the zip and the tar file
			exec( "cd fiddles; zip $file_name.zip $file_name.html; tar czf $file_name.tar.gz $file_name.html" );
		}

		// _executeDownload will take care file checking for us
		return _executeDownload( 'fiddles/' . $file_name . '.' . $type );
	}

	function _executeDownload( $file_name ) {
		for ($second = 0; ; $second++) {
			if ( $second >= 5 ) {
				return false;
			}

			try {
				if ( file_exists( $file_name ) ) {
					break;
				}
			} catch (Exception $e) {}
			sleep(1);
		}

		// http headers for zip downloads
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.str_replace( 'fiddles/', '', $file_name ) . '"');
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($file_name));
		ob_end_flush();
		readfile( $file_name );
		die( 'OK' );
	}
