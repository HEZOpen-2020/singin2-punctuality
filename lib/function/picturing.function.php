<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

/**
 * rgb 数组转换为整数
 */
function color_rgb2int($col) {
	if(isset($col[3])) {
		return ($col[0]<<16)|($col[1]<<8)|($col[2]<<0)|($col[3]<<24);
	}
	return ($col[0]<<16)|($col[1]<<8)|($col[2]<<0);
}

/**
 * 整数颜色转换为 rgb 数组
 */
function color_int2rgb($col) {
	return [
		($col>>16) & 0xFF,
		($col>>8) & 0xFF,
		($col>>0) & 0xFF,
		($col>>24) & 0x7F
	];
}

/**
 * 统一颜色表示
 */
function unify_color(&$col) {
	if(is_string($col)) {
		$col = _C('color.' . $col);
	}
	if(is_array($col)) {
		$col = color_rgb2int($col);
	}
}

/**
 * 长度单位转化为像素数
 * - c 厘米
 * - m 毫米
 * - p 0.1毫米
 * - 使用字段累加法，故相同单位可重复且支持加减法运算。
 */
function L($len) {
	if($len == '') {
		return 0;
	}
	$vals = [];
	$startpos = 0;
	for($i = 0; $i < strlen($len); $i++) {
		$ch = $len[$i];
		if($ch == 'c' || $ch == 'm' || $ch == 'p' || $ch == 'b' || $ch == 's' || $ch == 'e') {
			$vals[] = substr($len, $startpos, $i - $startpos + 1);
			$startpos = $i + 1;
		}
	}
	
	$ret = 0;
	foreach($vals as $val) {
		$val = trim($val);
		$unit = substr($val,strlen($val) - 1);
		$num = substr($val,0,strlen($val) - 1);
		$mult = 0;
		if($unit == 'c') {
			$mult = 10 * 10;
		} else if($unit == 'm') {
			$mult = 10;
		} else if($unit == 'p') {
			$mult = 1;
		} else if($unit == 'b') {
			$mult = 256;
		} else if($unit == 's') {
			$mult = 16;
		} else if($unit == 'e') {
			$mult = 1;
		}
		$mult *= 1;
		$ret += $mult * floatval($num);
	}
	
	return $ret;
}

/**
 * 统一长度表示
 */
function unify_length(&$len) {
	if(is_string($len)) {
		$len = L($len);
	}
}

/* 参数顺序：图内尺色位 */

/**
 * 创建带有背景色的图像
 */
function picture_create($bgcolor,$width,$height) {
	unify_length($width);
	unify_length($height);
	unify_color($bgcolor);
	
	$im = imageCreateTrueColor($width,$height);
	imageSaveAlpha($im,true);
	imageAlphaBlending($im, false);
	imageFilledRectangle($im,0,0,$width-1,$height-1,$bgcolor);
	imageAlphaBlending($im, true);
	
	return $im;
}

/**
 * 输出图像
 */
function picture_print(&$im) {
	$w = intval(imageSX($im) / 2);
	$h = intval(imageSY($im) / 2);
	
	// $st = imageCreateTrueColor($w,$h);
	// imageCopyResampled($st,$im,0,0,0,0,$w-1,$h-1,$w*2-1,$h*2-1);
	
	header('Content-Type: image/png');
	imagepng($im);
}

/**
 * 获取像素点颜色
 */
function picture_color_at(&$im,$x,$y) {
	unify_length($x);
	unify_length($y);
	return color_int2rgb(imageColorAt($im,$x,$y));
}

/**
 * 设置像素点颜色
 */
function picture_set_pixel(&$im,$col,$x,$y) {
	unify_color($col);
	unify_length($x);
	unify_length($y);
	imageSetPixel($im,$x,$y,$col);
	return $im;
}

/**
 * 绘制实心长方形
 * 参数顺序：图像、颜色、x1、y1、x2、y2
 * 注意：长方形不包含右下角处的点。
 */
function picture_rect(&$im,$col,$x1,$y1,$x2,$y2) {
	unify_color($col);
	unify_length($x1);
	unify_length($y1);
	unify_length($x2);
	unify_length($y2);
	
	if($x1 == $x2 || $y1 == $y2) {
		return $im;
	}
	if($x1 > $x2) {
		swap($x1, $x2);
	}
	if($y1 > $y2) {
		swap($y1, $y2);
	}
	$x2 -= 1;
	$y2 -= 1;
	
	imageFilledRectangle($im,$x1,$y1,$x2,$y2,$col);
	return $im;
}

/**
 * 绘制空心长方形
 */
function picture_rect_o(&$im,$col,$thick,$x1,$y1,$x2,$y2) {
	unify_color($col);
	unify_length($thick);
	unify_length($x1);
	unify_length($y1);
	unify_length($x2);
	unify_length($y2);
	
	if($x1 == $x2 || $y1 == $y2) {
		return $im;
	}
	if($x1 > $x2) {
		swap($x1, $x2);
	}
	if($y1 > $y2) {
		swap($y1, $y2);
	}
	$x2 -= 1;
	$y2 -= 1;
	
	$thick = min($thick,$x2-$x1+1,$y2-$y1+1);
	
	// 上
	imageFilledRectangle($im,$x1,$y1,$x2,$y1+$thick-1,$col);
	// 左
	imageFilledRectangle($im,$x1,$y1,$x1+$thick-1,$y2,$col);
	// 下
	imageFilledRectangle($im,$x1,$y2,$x2,$y2-$thick+1,$col);
	// 右
	imageFilledRectangle($im,$x2,$y1,$x2-$thick+1,$y2,$col);
	
	return $im;
}

/**
 * 绘制实心多边形
 */
function picture_polygon(&$im,$col,$points) {
	unify_color($col);
	$unified_points = [];
	$npoints = count($points);
	for($i = 0; $i < $npoints; $i++) {
		$unified_points[] = $points[$i][0];
		$unified_points[] = $points[$i][1];
		unify_length($unified_points[$i<<1]);
		unify_length($unified_points[$i<<1|1]);
	}
	imageFilledPolygon($im,$unified_points,$npoints,$col);
}

/**
 * 绘制平头粗线
 */
function picture_line(&$im,$thick,$col,$x1,$y1,$x2,$y2) {
	unify_color($col);
	unify_length($thick);
	unify_length($x1);
	unify_length($y1);
	unify_length($x2);
	unify_length($y2);
	
	$len = sqrt(($x2 - $x1) ** 2 + ($y2 - $y1) ** 2);
	$vec_t = [1,0];
	if($len > 0) {
		$vec_t = [($x2 - $x1) / $len, ($y2 - $y1) / $len];
	}
	$vec_n = [-$vec_t[1],$vec_t[0]];
	$thick *= 0.5;
	
	$points = [];
	$points[] = [$x1 + (-$vec_t[0]+$vec_n[0]) * $thick, $y1 + (-$vec_t[1]+$vec_n[1]) * $thick];
	$points[] = [$x1 + (-$vec_t[0]-$vec_n[0]) * $thick, $y1 + (-$vec_t[1]-$vec_n[1]) * $thick];
	$points[] = [$x2 + (+$vec_t[0]-$vec_n[0]) * $thick, $y2 + (+$vec_t[1]-$vec_n[1]) * $thick];
	$points[] = [$x2 + (+$vec_t[0]+$vec_n[0]) * $thick, $y2 + (+$vec_t[1]+$vec_n[1]) * $thick];
	
	picture_polygon($im,$col,$points);
}

/**
 * 绘制箭头
 */
function picture_arrow(&$im,$thick,$head_len,$col,$x1,$y1,$x2,$y2) {
	unify_color($col);
	unify_length($thick);
	unify_length($head_len);
	unify_length($x1);
	unify_length($y1);
	unify_length($x2);
	unify_length($y2);
	
	$len = sqrt(($x2 - $x1) ** 2 + ($y2 - $y1) ** 2);
	$vec_t = [1,0];
	if($len > 0) {
		$vec_t = [($x2 - $x1) / $len, ($y2 - $y1) / $len];
	}
	$vec_n = [-$vec_t[1],$vec_t[0]];
	
	$comp = 0.707107; // sqrt(0.5)
	$delta = ($thick) * (1 - $comp);
	$tip_point = [$x2 + $delta * $vec_t[0], $y2 + $delta * $vec_t[1]];
	$tail_1 = [
		$tip_point[0] + (-$vec_t[0]+$vec_n[0]) * $head_len * $comp,
		$tip_point[1] + (-$vec_t[1]+$vec_n[1]) * $head_len * $comp
	];
	$tail_2 = [
		$tip_point[0] + (-$vec_t[0]-$vec_n[0]) * $head_len * $comp,
		$tip_point[1] + (-$vec_t[1]-$vec_n[1]) * $head_len * $comp
	];
	
	picture_line($im,$thick,$col,$x1,$y1,$x2,$y2);;
	picture_line($im,$thick,$col,$tip_point[0],$tip_point[1],$tail_1[0],$tail_1[1]);
	picture_line($im,$thick,$col,$tip_point[0],$tip_point[1],$tail_2[0],$tail_2[1]);
}

/**
 * 图章
 * 参数顺序：图像-图章名-宽度-高度-x-y
 */
function picture_stamp(&$im,$stamp,$width,$height,$x,$y) {
	unify_length($width);
	unify_length($height);
	unify_length($x);
	unify_length($y);
	$stamp = STAMP . $stamp . '.png';
	
	$st = imageCreateFromPNG($stamp);
	$st_w = imageSX($st);
	$st_h = imageSY($st);
	imageCopyResampled($im,$st,$x,$y,0,0,$width,$height,$st_w-1,$st_h-1);
	return $im;
}

/**
 * 单色图章
 * 参数顺序：图像-图章名-宽度-高度-x-y
 */
function picture_stamp_colored(&$im,$stamp,$width,$height,$col,$x,$y,$grayscale=false) {
	unify_length($width);
	unify_length($height);
	unify_length($x);
	unify_length($y);
	unify_color($col);
	$col = color_int2rgb($col);
	$stamp = STAMP . $stamp . '.png';
	
	// 加载图章
	$src = imageCreateFromPNG($stamp);
	$st_w = imageSX($src);
	$st_h = imageSY($src);
	
	// 创建单色图样
	$st = imageCreateTrueColor($st_w,$st_h);
	imageSaveAlpha($st,true);
	imageAlphaBlending($st, false);
	imageFilledRectangle($st,0,0,$st_w-1,$st_h-1,color_rgb2int([0,0,0,127]));
	for($i = 0; $i < $st_w; $i++) {
		for($j = 0; $j < $st_h; $j++) {
			$curr_col = color_int2rgb(imageColorAt($src,$i,$j));
			$alpha = 0;
			if($grayscale) {
				$alpha = (127 - $curr_col[3]) * (0.2 * $curr_col[0] + 0.7 * $curr_col[1] + 0.1 * $curr_col[2]) / 255.0;
				$alpha = 127 - $alpha;
			} else {
				$alpha = $curr_col[3];
			}
			$curr_col = [$col[0],$col[1],$col[2],$alpha];
			imageSetPixel($st,$i,$j,color_rgb2int($curr_col));
		}
	}
	
	imageCopyResampled($im,$st,$x,$y,0,0,$width,$height,$st_w-1,$st_h-1);
	return $im;
}

/**
 * 在图像上打印文字
 * 参数顺序：图像、文本、字体、尺寸、角度、颜色、x、y
 * （提示：图像-内容-尺寸-色彩-位置）
 */
function picture_text(&$im,$text,$font,$size,$angle,$col,$x,$y,$anchor='left',$wanchor='default') {
	unify_color($col);
	unify_length($x);
	unify_length($y);
	unify_length($size);
	$font = FONT . _C('font.' . $font) . '.ttf';
	
	// 首字位修正
	if($wanchor != 'default') {
		$rect = imagettfbbox($size,$angle,$font,'1测'); // 文字取中文与西文字符
		$dx = +($rect[0] - $rect[6]);
		$dy = +($rect[1] - $rect[7]);
		$x += $dx;
		$y += $dy;
	}
	
	$rect = imagettfbbox($size,$angle,$font,$text);
	$weight_h = 0;
	if($anchor == 'mid') {
		$weight_h = 0.5;
	} else if($anchor == 'right') {
		$weight_h = 1;
	}
	$weight_v = 0;
	if($wanchor == 'mid') {
		$weight_v = 0.5;
	} else if($wanchor == 'bottom') {
		$weight_v = 1;
	}
	$dx = +(($rect[2] - $rect[0]) * $weight_h) +(($rect[0] - $rect[6]) * $weight_v);
	$dy = +(($rect[3] - $rect[1]) * $weight_h) +(($rect[1] - $rect[7]) * $weight_v);
	$x -= $dx;
	$y -= $dy;
	
	imagettftext(
		$im,
		$size,
		$angle,
		$x,
		$y,
		$col,
		$font,
		$text
	);

	if($anchor == 'left') return [$x + $rect[2] - $rect[0], $y + $rect[3] - $rect[1]];
	if($anchor == 'right') return [$x,$y];
	return -1;
}

/**
 * 图元：线路标记
 */
function draw_linemark(&$im,$height,$color,$in_range,$x,$y,$desired_width=-1) {
	unify_color($color);
	unify_length($height);
	unify_length($x);
	unify_length($y);
	
	$width = $height / 4.5 * 1.3;
	if($desired_width >= 0) {
		$width = $desired_width;
	}
	$bgcolor = picture_color_at($im,$x,$y);
	
	picture_rect($im,$color,$x,$y,$x+$width,$y-$height);
	if(!$in_range) {
		picture_polygon($im,$bgcolor,[
			[$x,$y-$height*0.15],
			[$x,$y-$height*0.3],
			[$x+$width,$y-$height*0.45],
			[$x+$width,$y-$height*0.3],
		]);
		picture_polygon($im,$bgcolor,[
			[$x+$width,$y-$height*0.7],
			[$x+$width,$y-$height*0.85],
			[$x,$y-$height*0.7],
			[$x,$y-$height*0.55],
		]);
	}
	
	return $im;
}

/**
 * 图元：出口标记
 */
function draw_exitmark(&$im,$exit_id,$size,$color,$x,$y) {
	unify_color($color);
	unify_length($size);
	unify_length($x);
	unify_length($y);
	
	$exit_letter = rtrim($exit_id,'0123456789');
	$exit_num = substr($exit_id,strlen($exit_letter));
	
	$line_width = $size / 4.8 * 0.3;
	$font_size = $size / 4.8 * 2.8;
	$font_size_sub = $size / 4.8 * 1.2;
	
	if($exit_letter == '出') {
		picture_rect($im,'exit',$x,$y,$x+$size,$y+$size);
	}
	picture_rect_o($im,$color,$line_width,$x,$y,$x+$size,$y+$size);
	if($exit_num == '') {
		$left_p = $size / 4.8 * 2.4;
		$top_p = $size / 4.8 * 1.35;
		if($exit_letter == '出') {
			$top_p = $size / 4.8 * 1.85;
		}
		picture_text($im,$exit_letter,'exitmark',$font_size,0,$color,$x+$left_p,$y+$top_p,'mid','mid');
	} else {
		if(strlen($exit_num) > 1) {
			$exit_num = '#';
		}
		$left_p = $size / 4.8 * 2.05;
		$top_p = $size / 4.8 * 1.35;
		if($exit_letter == '出') {
			$top_p = $size / 4.8 * 1.85;
		}
		picture_text($im,$exit_letter,'exitmark',$font_size,0,$color,$x+$left_p,$y+$top_p,'mid','mid');
		$left_p = $size / 4.8 * 3.75;
		$top_p = $size / 4.8 * 2.7;
		picture_text($im,$exit_num,'exitmark',$font_size_sub,0,$color,$x+$left_p,$y+$top_p,'mid','mid');
	}
	
	return $im;
}

/**
 * 图元：出口标签
 */
function draw_exit_tags(&$im,$tags,$size,$margin,$color,$x,$y,$anchor='left') {
	unify_length($size);
	unify_length($margin);
	unify_length($x);
	unify_length($y);
	unify_color($color);

	$n = count($tags);
	$total_w = ($size + $margin) * $n - $margin;
	if($anchor == 'right') {
		$x -= $total_w;
	} else if($anchor == 'mid') {
		$x -= 0.5 * $total_w;
	}
	
	foreach($tags as $tag) {
		picture_stamp_colored($im,'exit/' . $tag,$size,$size,$color,$x,$y,true);
		$x += $size + $margin;
	}
	
	return $im;
}

/**
 * 图元：线路标记
 * - 注意由于图元性质与文本类似，锚点在左下角
 */
function draw_line_tags(&$im,$data,$height,$color,$x,$y,$anchor='left') {
	unify_length($height);
	unify_length($x);
	unify_length($y);
	unify_color($color);
	
	$n = count($data);
	$text_lmar = $height / 4.5 * 1.5;
	$text_bmar = $height / 4.5 * 0.2;
	$text_size = $height / 4.5 * 1.8;
	$width = $height / 4.5 * 4.8;
	$total_w = $width * $n - $height / 4.5 * 0.7;
	if($anchor == 'right') {
		$x -= $total_w;
	} else if($anchor == 'mid') {
		$x -= 0.5 * $total_w;
	}
	
	foreach($data as $item) {
		$line_id = $item['line'];
		$line = get_line($line_id);
		draw_linemark($im,$height,$line['color'],$item['in_range'],$x,$y);
		picture_text($im,$line['name'][2],'linemark',$text_size,0,$color,$x+$text_lmar,$y-$text_bmar);
		$x += $width;
	}
	
	return $im;
}

/**
 * 图元：指路箭头
 */
function draw_pointing_arrow(&$im,$dir,$size,$color,$x,$y) {
	unify_length($size);
	unify_length($x);
	unify_length($y);
	unify_color($color);
	
	$thick = $size * 0.14;
	$q2 = 0.707107;
	$s2 = 1.414214;
	
	if(in_array($dir,['L'])) {
		picture_arrow($im,$thick,$size*0.4,$color,$x+$size-0.5*$thick,$y+0.5*$size,$x+$thick,$y+0.5*$size);
	} else if(in_array($dir,['R'])) {
		picture_arrow($im,$thick,$size*0.4,$color,$x+0.5*$thick,$y+0.5*$size,$x+$size-$thick,$y+0.5*$size);
	} else if(in_array($dir,['LT'])) {
		picture_line($im,$thick,$color,$x+$size-0.5*$thick,$y+0.9*$size,$x+$size-0.5*$thick,$y+0.3*$size);
		picture_arrow($im,$thick,$size*0.3,$color,$x+$size-0.5*$thick,$y+0.3*$size,$x+$thick,$y+0.3*$size);
	} else if(in_array($dir,['RT'])) {
		picture_line($im,$thick,$color,$x+0.5*$thick,$y+0.9*$size,$x+0.5*$thick,$y+0.3*$size);
		picture_arrow($im,$thick,$size*0.3,$color,$x+0.5*$thick,$y+0.3*$size,$x+$size-$thick,$y+0.3*$size);
	} else if(in_array($dir,['F','U'])) {
		picture_arrow($im,$thick,$size*0.4,$color,$x+0.5*$size,$y+$size-0.5*$thick,$x+0.5*$size,$y+$thick);
	} else if(in_array($dir,['B','D'])) {
		picture_arrow($im,$thick,$size*0.4,$color,$x+0.5*$size,$y+0.5*$thick,$x+0.5*$size,$y+$size-$thick);
	} else {
		$x += 0.1*$size;
		$y += 0.1*$size;
		$size *= 0.8;
		if(in_array($dir,['RD'])) {
			picture_arrow($im,$thick,$size*0.566,$color,$x+$q2*$thick,$y+$q2*$thick,$x+$size-$q2*$thick,$y+$size-$q2*$thick);
		} else if(in_array($dir,['RU'])) {
			picture_arrow($im,$thick,$size*0.566,$color,$x+$q2*$thick,$y+$size-$q2*$thick,$x+$size-$q2*$thick,$y+$q2*$thick);
		} else if(in_array($dir,['LU'])) {
			picture_arrow($im,$thick,$size*0.566,$color,$x+$size-$q2*$thick,$y+$size-$q2*$thick,$x+$q2*$thick,$y+$q2*$thick);
		} else if(in_array($dir,['LD'])) {
			picture_arrow($im,$thick,$size*0.566,$color,$x+$size-$q2*$thick,$y+$q2*$thick,$x+$q2*$thick,$y+$size-$q2*$thick);
		}
	}
}
