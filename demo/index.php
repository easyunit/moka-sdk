<!DOCTYPE html>
<html>

<head>
	<title>简单的文件浏览&&视频播放</title>
	<meta charset="utf-8">
</head>

<body>
	<div>
		<center>

			<?php
			// 响应点击视频   isset() 变量是否存在
			if ($action = isset($_GET["action"])) {
			}
			if ($action == "player") {
				$filename = $_GET["filename"];
				echo "<video id='video' src='{$filename}' controls='controls' height='800' ></video>";

				$speed = <<<EOF
				<p>选择播放速率：
				<select id="selRate">
				<option value="1" selected>1.0	</option>

				<option value="0.5">	0.5		</option>
				<option value="1.25">	1.25	</option>
				<option value="1.5">	1.5		</option>
				<option value="1.75">	1.75	</option>
				<option value="2">		2.0		</option>
				<option value="2.25">	2.25	</option>
				<option value="2.5">	2.5		</option>
				<option value="3">		3.0		</option>
				<option value="4">		4.0		</option>
				</select>
				</p>
				<p> <button id="btnPlay">视频播放</button> </p>
EOF;
				echo $speed;
			}
			?>
		</center>
	</div>

	<div>
		<center>
			<h2>简单的文件浏览&&视频播放</h2>
			<hr width="90%">
			<!-- <a href='index.php?$action=add'>创建文件</a> -->
			<br>
			<br>
			<table width="400" border="1">
				<tr BGCOLOR="#CCCCCC">
					<th>序号</th>
					<th>文件名称</th>
				</tr>

				<?php
				//设置目录为当前目录
				$dir = "./";
				//设置要过滤不显示的文件
				$config = array();
				$config['filelist'][] = '.';
				$config['filelist'][] = '..';
				$config['filelist'][] = '.DS_Store';
				$config['filelist'][] = '.idea';
				$config['filelist'][] = 'index.php';
				$config['filelist'][] = '文件浏览器.php';
				$config['filelist'][] = '目录浏览.php';
				$config['filelist'][] = '.Spotlight-V100';
				$config['filelist'][] = '.DocumentRevisions-V100';
				$config['filelist'][] = '.TemporaryItems';
				$config['filelist'][] = '.Trashes';
				$config['filelist'][] = '.fseventsd';

				if (is_dir($dir)) {
					// 判断是否是一个目录
					if ($dh = opendir($dir)) {
						// 打开目录
						$i = 0; // 文件的序号
						while (($file = readdir($dh)) !== false) {
							// 循环读取目录的内容 并传递给$file
							if (in_array($file, $config['filelist'])) { // 判断是否在数组中 如果是则不执行
								continue; //跳出本次循环 继续下次遍历
							} else {
								$files[$i]["name"] = $file; // 将文件名添加进数组中
								//$files[$i]["size"] = round((filesize($file)/1024),2);//获取文件大小
								//$files[$i]["time"] = date("Y-m-d H:i:s",filemtime($file));//获取文件最近修改日期
								$i++;
							}
						}
					}
					closedir($dh); // 关闭目录
					sort($files); // 对文件名称数组进行排序

					foreach ($files as $k => $v) {
						echo "<tr>";
						// echo '<tr BGCOLOR="#eeeeee">';
						echo "<td>" . $k . "</td>";

						// 以小数点分割文件后缀 转化成数组 并且弹出数组的最后一项 如果后缀是MP4，则调用在线播放器
						$file_name_array = explode('.', $v['name']);
						$file_suffix = array_pop($file_name_array);

						if ($file_suffix == "mp4") {
							echo "<td><a href='" . "index.php?action=player&filename={$v['name']}" . "'>" . $v['name'] . "</a></td>"; // 处理视频点击 js可以用onclick php请用get与post
						} else {
							echo "<td><a href='{$v['name']}'>" . $v['name'] . "</a></td>"; // 显示文件名和超链接
						}

						echo "</tr>";
					}
				}
				?>

				<!-- 底部表格行 跨5列 -->
				<tr BGCOLOR="#CCCCCC">
					<th colspan="5">&nbsp</th>
				</tr>
			</table>
		</center>
	</div>


	<script type="text/javascript">
		// 处理id为video的视频

		var eleSelect = document.getElementById('selRate');
		var eleButton = document.getElementById('btnPlay');
		// 视频元素
		var video = document.getElementById('video');
		// 改变播放速率
		eleSelect.addEventListener('change', function() {
			video.playbackRate = this.value;
		});
		// 点击播放按钮
		eleButton.addEventListener('click', function() {
			video.play();
		});
	</script>

	<?php
	phpinfo();
	?>

</body>

</html>