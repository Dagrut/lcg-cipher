<?php

/*
Copyright (c) 2014, Maxime Ferrino
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. All advertising materials mentioning features or use of this software
   must display the following acknowledgement:
   This product includes software developed by the author.
4. Neither the name of the copyright holder nor the
   names of its contributors may be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTOR(S) ''AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE AUTHOR AND CONTRIBUTOR(S) BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class LCG {
	public $a;
	public $x;
	public $c;
	public $m;
	
	function __construct($a, $x, $c, $m) {
		$this->a = $a;
		$this->x = $x;
		$this->c = $c;
		$this->m = $m;
	}
	
	function gen() {
		$this->x = ($this->a * $this->x + $this->c) % $this->m;
		return($this->x);
	}
}

class LCGCipher {
	private $modulos = array(
		2147433649, 2147433667, 2147433697, 2147433703, 2147433707, 2147433719, 
		2147433779, 2147433791, 2147433811, 2147433887, 2147433929, 2147433943, 
		2147433947, 2147433991, 2147434001, 2147434007, 2147434013, 2147434043, 
		2147434049, 2147434063, 2147434099, 2147434103, 2147434109, 2147434117, 
		2147434127, 2147434141, 2147434153, 2147434193, 2147434217, 2147434243, 
		2147434279, 2147434291, 2147434309, 2147434361, 2147434363, 2147434391, 
		2147434411, 2147434439, 2147434451, 2147434477, 2147434483, 2147434507, 
		2147434517, 2147434519, 2147434529, 2147434561, 2147434579, 2147434603, 
		2147434651, 2147434673, 2147434687, 2147434691, 2147434703, 2147434727, 
		2147434733, 2147434771, 2147434811, 2147434829, 2147434871, 2147434879, 
		2147434907, 2147434909, 2147434943, 2147434979, 2147434981, 2147435009, 
		2147435039, 2147435057, 2147435063, 2147435071, 2147435083, 2147435141, 
		2147435149, 2147435161, 2147435201, 2147435243, 2147435249, 2147435261, 
		2147435281, 2147435287, 2147435293, 2147435299, 2147435321, 2147435327, 
		2147435371, 2147435417, 2147435431, 2147435473, 2147435483, 2147435491, 
		2147435501, 2147435509, 2147435527, 2147435533, 2147435539, 2147435569, 
		2147435599, 2147435623, 2147435701, 2147435737, 2147435777, 2147435783, 
		2147435789, 2147435791, 2147435809, 2147435861, 2147435891, 2147435893, 
		2147435897, 2147435911, 2147435929, 2147435951, 2147435957, 2147435959, 
		2147435977, 2147435987, 2147436019, 2147436037, 2147436041, 2147436047, 
		2147436079, 2147436091, 2147436103, 2147436119, 2147436143, 2147436197, 
		2147436199, 2147436217, 2147436281, 2147436337, 2147436353, 2147436397, 
		2147436419, 2147436427, 2147436443, 2147436463, 2147436517, 2147436559, 
		2147436611, 2147436659, 2147436667, 2147436701, 2147436751, 2147436793, 
		2147436833, 2147436871, 2147436877, 2147436901, 2147436919, 2147436931, 
		2147436959, 2147436989, 2147436997, 2147437001, 2147437027, 2147437069, 
		2147437073, 2147437079, 2147437147, 2147437181, 2147437183, 2147437217, 
		2147437267, 2147437283, 2147437291, 2147437429, 2147437447, 2147437451, 
		2147437493, 2147437511, 2147437531, 2147437553, 2147437577, 2147437583, 
		2147437601, 2147437609, 2147437613, 2147437651, 2147437723, 2147437751, 
		2147437753, 2147437769, 2147437777, 2147437801, 2147437811, 2147437819, 
		2147437843, 2147437871, 2147437889, 2147437937, 2147437997, 2147438003, 
		2147438009, 2147438053, 2147438057, 2147438077, 2147438093, 2147438123, 
		2147438143, 2147438179, 2147438207, 2147438261, 2147438303, 2147438347, 
		2147438353, 2147438357, 2147438389, 2147438393, 2147438437, 2147438441, 
		2147438459, 2147438483, 2147438519, 2147438563, 2147438599, 2147438617, 
		2147438633, 2147438663, 2147438681, 2147438687, 2147438707, 2147438717, 
		2147438731, 2147438743, 2147438749, 2147438833, 2147438869, 2147438873, 
		2147438897, 2147438927, 2147438941, 2147438987, 2147439017, 2147439061, 
		2147439143, 2147439149, 2147439187, 2147439209, 2147439227, 2147439251, 
		2147439271, 2147439293, 2147439323, 2147439331, 2147439341, 2147439401, 
		2147439407, 2147439443, 2147439491, 2147439509, 2147439523, 2147439557, 
		2147439583, 2147439599, 2147439653, 2147439709, 2147439727, 2147439731, 
		2147439799, 2147439803, 2147439841, 2147439859, 2147439863, 2147439883, 
		2147439911, 2147439923, 2147439953, 2147439961, 2147439979, 2147439989, 
		2147439991, 2147440027, 2147440039, 2147440049, 2147440069, 2147440109, 
		2147440117, 2147440201, 2147440219, 2147440247, 2147440261, 2147440349, 
		2147440367, 2147440397, 2147440409, 2147440423, 2147440429, 2147440433, 
		2147440441, 2147440469, 2147440549, 2147440579, 2147440619, 2147440621, 
		2147440637, 2147440639, 2147440643, 2147440699, 2147440703, 2147440721, 
		2147440733, 2147440751, 2147440829, 2147440837, 2147440843, 2147440847, 
		2147440849, 2147440853, 2147440873, 2147440903, 2147440921, 2147440943, 
		2147440957, 2147440961, 2147440973, 2147440979, 2147440991, 2147441033, 
		2147441063, 2147441099, 2147441119, 2147441137, 2147441141, 2147441147, 
		2147441159, 2147441167, 2147441189, 2147441201, 2147441209, 2147441239, 
		2147441251, 2147441273, 2147441281, 2147441311, 2147441347, 2147441357, 
		2147441399, 2147441423, 2147441449, 2147441453, 2147441501, 2147441507, 
		2147441519, 2147441531, 2147441539, 2147441563, 2147441587, 2147441623, 
		2147441671, 2147441701, 2147441711, 2147441749, 2147441767, 2147441831, 
		2147441833, 2147441839, 2147441843, 2147441867, 2147441869, 2147441893, 
		2147441903, 2147441963, 2147441971, 2147441977, 2147441999, 2147442029, 
		2147442043, 2147442053, 2147442061, 2147442071, 2147442119, 2147442131, 
		2147442139, 2147442179, 2147442191, 2147442203, 2147442221, 2147442277, 
		2147442289, 2147442299, 2147442331, 2147442347, 2147442391, 2147442413, 
		2147442419, 2147442433, 2147442457, 2147442467, 2147442469, 2147442487, 
		2147442511, 2147442523, 2147442559, 2147442613, 2147442623, 2147442629, 
		2147442637, 2147442673, 2147442677, 2147442697, 2147442751, 2147442763, 
		2147442767, 2147442797, 2147442809, 2147442811, 2147442823, 2147442827, 
		2147442851, 2147442893, 2147442901, 2147442931, 2147442961, 2147442977, 
		2147442989, 2147443003, 2147443013, 2147443021, 2147443033, 2147443049, 
		2147443057, 2147443087, 2147443121, 2147443157, 2147443163, 2147443169, 
		2147443171, 2147443211, 2147443223, 2147443283, 2147443303, 2147443339, 
		2147443343, 2147443357, 2147443373, 2147443391, 2147443399, 2147443411, 
		2147443421, 2147443423, 2147443471, 2147443513, 2147443523, 2147443537, 
		2147443547, 2147443561, 2147443567, 2147443579, 2147443589, 2147443603, 
		2147443633, 2147443679, 2147443721, 2147443729, 2147443747, 2147443759, 
		2147443787, 2147443789, 2147443799, 2147443913, 2147443931, 2147444017, 
		2147444021, 2147444041, 2147444071, 2147444077, 2147444081, 2147444107, 
		2147444153, 2147444161, 2147444177, 2147444213, 2147444251, 2147444279, 
		2147444291, 2147444293, 2147444317, 2147444333, 2147444359, 2147444389, 
		2147444399, 2147444441, 2147444459, 2147444479, 2147444501, 2147444561, 
		2147444567, 2147444569, 2147444603, 2147444609, 2147444633, 2147444647, 
		2147444669, 2147444687, 2147444713, 2147444773, 2147444791, 2147444797, 
		2147444821, 2147444833, 2147444843, 2147444851, 2147444891, 2147444917, 
		2147444941, 2147444963, 2147444977, 2147445011, 2147445029, 2147445077, 
		2147445101, 2147445103, 2147445119, 2147445121, 2147445137, 2147445143, 
		2147445173, 2147445191, 2147445203, 2147445211, 2147445239, 2147445277, 
		2147445283, 2147445317, 2147445319, 2147445343, 2147445373, 2147445401, 
		2147445427, 2147445437, 2147445449, 2147445473, 2147445497, 2147445533, 
		2147445539, 2147445541, 2147445589, 2147445631, 2147445689, 2147445691, 
		2147445709, 2147445787, 2147445799, 2147445823, 2147445851, 2147445869, 
		2147445887, 2147445893, 2147445913, 2147445917, 2147445943, 2147445973, 
		2147445983, 2147446003, 2147446009, 2147446033, 2147446039, 2147446057, 
		2147446079, 2147446087, 2147446097, 2147446141, 2147446151, 2147446159, 
		2147446163, 2147446181, 2147446193, 2147446247, 2147446283, 2147446289, 
		2147446297, 2147446303, 2147446321, 2147446337, 2147446339, 2147446351, 
		2147446361, 2147446373, 2147446391, 2147446439, 2147446489, 2147446571, 
		2147446619, 2147446657, 2147446661, 2147446667, 2147446669, 2147446673, 
		2147446687, 2147446699, 2147446793, 2147446831, 2147446837, 2147446841, 
		2147446843, 2147446871, 2147446921, 2147446927, 2147446943, 2147446963, 
		2147446991, 2147446993, 2147446997, 2147447011, 2147447017, 2147447101, 
		2147447171, 2147447179, 2147447213, 2147447231, 2147447243, 2147447251, 
		2147447261, 2147447321, 2147447333, 2147447339, 2147447347, 2147447359, 
		2147447363, 2147447383, 2147447389, 2147447411, 2147447453, 2147447459, 
		2147447473, 2147447573, 2147447597, 2147447609, 2147447639, 2147447641, 
		2147447657, 2147447669, 2147447723, 2147447747, 2147447777, 2147447791, 
		2147447807, 2147447833, 2147447881, 2147447899, 2147447917, 2147447933, 
		2147447977, 2147447987, 2147448013, 2147448031, 2147448049, 2147448059, 
		2147448071, 2147448091, 2147448101, 2147448181, 2147448197, 2147448253, 
		2147448257, 2147448287, 2147448301, 2147448323, 2147448353, 2147448367, 
		2147448379, 2147448409, 2147448427, 2147448467, 2147448473, 2147448539, 
		2147448577, 2147448581, 2147448593, 2147448629, 2147448671, 2147448679, 
		2147448701, 2147448703, 2147448707, 2147448713, 2147448731, 2147448733, 
		2147448749, 2147448757, 2147448763, 2147448851, 2147448881, 2147448887, 
		2147448899, 2147448907, 2147448929, 2147448937, 2147448967, 2147448977, 
		2147448991, 2147449013, 2147449033, 2147449039, 2147449069, 2147449079, 
		2147449133, 2147449151, 2147449153, 2147449193, 2147449207, 2147449219, 
		2147449229, 2147449279, 2147449309, 2147449321, 2147449357, 2147449417, 
		2147449501, 2147449517, 2147449567, 2147449589, 2147449607, 2147449631, 
		2147449649, 2147449721, 2147449723, 2147449747, 2147449751, 2147449753, 
		2147449769, 2147449783, 2147449789, 2147449823, 2147449847, 2147449849, 
		2147449891, 2147449937, 2147449961, 2147449999, 2147450027, 2147450113, 
		2147450159, 2147450171, 2147450189, 2147450209, 2147450213, 2147450267, 
		2147450287, 2147450293, 2147450323, 2147450369, 2147450411, 2147450419, 
		2147450483, 2147450491, 2147450519, 2147450521, 2147450579, 2147450581, 
		2147450597, 2147450603, 2147450621, 2147450629, 2147450647, 2147450677, 
		2147450681, 2147450689, 2147450707, 2147450717, 2147450741, 2147450777, 
		2147450779, 2147450801, 2147450803, 2147450827, 2147450843, 2147450861, 
		2147450923, 2147450933, 2147450947, 2147450993, 2147451043, 2147451049, 
		2147451083, 2147451107, 2147451121, 2147451133, 2147451139, 2147451199, 
		2147451211, 2147451263, 2147451289, 2147451311, 2147451329, 2147451349, 
		2147451359, 2147451367, 2147451389, 2147451409, 2147451413, 2147451443, 
		2147451457, 2147451461, 2147451517, 2147451563, 2147451569, 2147451571, 
		2147451601, 2147451617, 2147451643, 2147451659, 2147451671, 2147451697, 
		2147451739, 2147451751, 2147451797, 2147451829, 2147451857, 2147451881, 
		2147451899, 2147451923, 2147451937, 2147451947, 2147451961, 2147452009, 
		2147452031, 2147452033, 2147452049, 2147452061, 2147452093, 2147452127, 
		2147452129, 2147452147, 2147452171, 2147452187, 2147452193, 2147452211, 
		2147452217, 2147452249, 2147452261, 2147452303, 2147452331, 2147452343, 
		2147452369, 2147452387, 2147452421, 2147452457, 2147452469, 2147452471, 
		2147452487, 2147452501, 2147452543, 2147452547, 2147452591, 2147452621, 
		2147452627, 2147452651, 2147452721, 2147452757, 2147452793, 2147452807, 
		2147452831, 2147452847, 2147452877, 2147452883, 2147452897, 2147452921, 
		2147452949, 2147452973, 2147452991, 2147452999, 2147453047, 2147453057, 
		2147453081, 2147453137, 2147453159, 2147453197, 2147453233, 2147453237, 
		2147453251, 2147453257, 2147453281, 2147453309, 2147453317, 2147453327, 
		2147453339, 2147453387, 2147453401, 2147453419, 2147453431, 2147453467, 
		2147453507, 2147453527, 2147453531, 2147453551, 2147453557, 2147453563, 
		2147453599, 2147453639, 2147453647, 2147453653, 2147453657, 2147453687, 
		2147453713, 2147453729, 2147453779, 2147453797, 2147453801, 2147453809, 
		2147453831, 2147453849, 2147453873, 2147453881, 2147453897, 2147453911, 
		2147453933, 2147453939, 2147453963, 2147453993, 2147453999, 2147454019, 
		2147454073, 2147454131, 2147454149, 2147454163, 2147454217, 2147454241, 
		2147454247, 2147454307, 2147454341, 2147454343, 2147454367, 2147454377, 
		2147454383, 2147454391, 2147454403, 2147454481, 2147454493, 2147454509, 
		2147454511, 2147454521, 2147454523, 2147454539, 2147454563, 2147454629, 
		2147454643, 2147454713, 2147454761, 2147454763, 2147454809, 2147454833, 
		2147454839, 2147454851, 2147454937, 2147454941, 2147454973, 2147454997, 
		2147455003, 2147455019, 2147455021, 2147455027, 2147455043, 2147455061, 
		2147455081, 2147455087, 2147455091, 2147455103, 2147455111, 2147455151, 
		2147455169, 2147455229, 2147455231, 2147455241, 2147455253, 2147455267, 
		2147455291, 2147455301, 2147455313, 2147455357, 2147455361, 2147455363, 
		2147455403, 2147455459, 2147455477, 2147455489, 2147455501, 2147455517, 
		2147455523, 2147455547, 2147455567, 2147455571, 2147455603, 2147455613, 
		2147455631, 2147455663, 2147455699, 2147455703, 2147455721, 2147455741, 
		2147455831, 2147455837, 2147455853, 2147455889, 2147455903, 2147455907, 
		2147455969, 2147455993, 2147456011, 2147456023, 2147456063, 2147456089, 
		2147456107, 2147456117, 2147456141, 2147456161, 2147456189, 2147456299, 
		2147456317, 2147456393, 2147456401, 2147456417, 2147456489, 2147456501, 
		2147456533, 2147456543, 2147456599, 2147456603, 2147456611, 2147456621, 
		2147456659, 2147456671, 2147456681, 2147456683, 2147456693, 2147456737, 
		2147456789, 2147456797, 2147456867, 2147456869, 2147456879, 2147456887, 
		2147456903, 2147456911, 2147456923, 2147456929, 2147456957, 2147456963, 
		2147456981, 2147457049, 2147457061, 2147457071, 2147457073, 2147457113, 
		2147457149, 2147457173, 2147457199, 2147457211, 2147457217, 2147457227, 
		2147457229, 2147457239, 2147457241, 2147457259, 2147457283, 2147457293, 
		2147457313, 2147457343, 2147457371, 2147457383, 2147457407, 2147457413, 
		2147457421, 2147457427, 2147457437, 2147457439, 2147457469, 2147457517, 
		2147457547, 2147457563, 2147457623, 2147457659, 2147457673, 2147457677, 
		2147457679, 2147457701, 2147457707, 2147457709, 2147457737, 2147457749, 
		2147457757, 2147457769, 2147457791, 2147457811, 2147457817, 2147457839, 
		2147457841, 2147457853, 2147457887, 2147457889, 2147457913, 2147457959, 
		2147457967, 2147457973, 2147458031, 2147458051, 2147458063, 2147458067, 
		2147458073, 2147458099, 2147458129, 2147458163, 2147458171, 2147458231, 
		2147458277, 2147458283, 2147458297, 2147458331, 2147458349, 2147458373, 
		2147458387, 2147458393, 2147458463, 2147458487, 2147458507, 2147458541, 
		2147458543, 2147458583, 2147458601, 2147458627, 2147458631, 2147458693, 
		2147458699, 2147458711, 2147458723, 2147458757, 2147458759, 2147458769, 
		2147458777, 2147458801, 2147458849, 2147458867, 2147458879, 2147458889, 
		2147458897, 2147458967, 2147458981, 2147458991, 2147458997, 2147459047, 
		2147459053, 2147459089, 2147459117, 2147459131, 2147459137, 2147459161, 
		2147459183, 2147459203, 2147459213, 2147459263, 2147459267, 2147459269, 
		2147459299, 2147459333, 2147459339, 2147459341, 2147459357, 2147459359, 
		2147459387, 2147459389, 2147459393, 2147459399, 2147459437, 2147459441, 
		2147459473, 2147459537, 2147459543, 2147459557, 2147459579, 2147459617, 
		2147459701, 2147459707, 2147459711, 2147459723, 2147459731, 2147459753, 
		2147459779, 2147459833, 2147459843, 2147459849, 2147459851, 2147459887, 
		2147459917, 2147459959, 2147459969, 2147459981, 2147459987, 2147459999, 
		2147460013, 2147460017, 2147460019, 2147460041, 2147460089, 2147460137, 
		2147460151, 2147460173, 2147460187, 2147460197, 2147460223, 2147460233, 
		2147460253, 2147460269, 2147460277, 2147460299, 2147460373, 2147460379, 
		2147460421, 2147460431, 2147460437, 2147460449, 2147460457, 2147460461, 
		2147460467, 2147460547, 2147460569, 2147460589, 2147460611, 2147460629, 
		2147460631, 2147460641, 2147460659, 2147460671, 2147460683, 2147460703, 
		2147460709, 2147460773, 2147460779, 2147460781, 2147460811, 2147460829, 
		2147460851, 2147460857, 2147460869, 2147460877, 2147460899, 2147460911, 
		2147460919, 2147460947, 2147460967, 2147460983, 2147461007, 2147461021, 
		2147461037, 2147461039, 2147461051, 2147461061, 2147461103, 2147461133, 
		2147461157, 2147461177, 2147461189, 2147461201, 2147461207, 2147461273, 
		2147461279, 2147461289, 2147461297, 2147461313, 2147461361, 2147461363, 
		2147461423, 2147461483, 2147461487, 2147461499, 2147461559, 2147461619, 
		2147461621, 2147461651, 2147461691, 2147461727, 2147461781, 2147461783, 
		2147461787, 2147461793, 2147461807, 2147461817, 2147461837, 2147461843, 
		2147461847, 2147461889, 2147461891, 2147461933, 2147461937, 2147461973, 
		2147461991, 2147462017, 2147462027, 2147462029, 2147462047, 2147462063, 
		2147462077, 2147462081, 2147462089, 2147462111, 2147462123, 2147462143, 
		2147462147, 2147462159, 2147462173, 2147462179, 2147462189, 2147462197, 
		2147462227, 2147462231, 2147462257, 2147462281, 2147462297, 2147462299, 
		2147462323, 2147462357, 2147462381, 2147462393, 2147462419, 2147462497, 
		2147462539, 2147462543, 2147462567, 2147462579, 2147462587, 2147462621, 
		2147462623, 2147462633, 2147462693, 2147462701, 2147462717, 2147462747, 
		2147462753, 2147462809, 2147462833, 2147462861, 2147462881, 2147462923, 
		2147462951, 2147462981, 2147463007, 2147463023, 2147463047, 2147463053, 
		2147463077, 2147463121, 2147463133, 2147463151, 2147463161, 2147463167, 
		2147463181, 2147463203, 2147463221, 2147463251, 2147463257, 2147463259, 
		2147463271, 2147463293, 2147463299, 2147463319, 2147463347, 2147463361, 
		2147463401, 2147463407, 2147463421, 2147463449, 2147463491, 2147463499, 
		2147463547, 2147463553, 2147463569, 2147463599, 2147463631, 2147463641, 
		2147463673, 2147463691, 2147463727, 2147463737, 2147463761, 2147463767, 
		2147463863, 2147463889, 2147463917, 2147463943, 2147463973, 2147464003, 
		2147464009, 2147464013, 2147464043, 2147464049, 2147464061, 2147464087, 
		2147464103, 2147464129, 2147464133, 2147464171, 2147464211, 2147464219, 
		2147464243, 2147464301, 2147464307, 2147464331, 2147464337, 2147464351, 
		2147464393, 2147464409, 2147464411, 2147464447, 2147464489, 2147464511, 
		2147464513, 2147464549, 2147464559, 2147464567, 2147464589, 2147464597, 
		2147464603, 2147464609, 2147464619, 2147464661, 2147464681, 2147464687, 
		2147464729, 2147464747, 2147464751, 2147464777, 2147464783, 2147464807, 
		2147464841, 2147464903, 2147464961, 2147465009, 2147465087, 2147465153, 
		2147465161, 2147465189, 2147465197, 2147465213, 2147465227, 2147465233, 
		2147465239, 2147465267, 2147465321, 2147465339, 2147465351, 2147465363, 
		2147465407, 2147465413, 2147465423, 2147465431, 2147465471, 2147465473, 
		2147465477, 2147465531, 2147465549, 2147465563, 2147465597, 2147465599, 
		2147465609, 2147465647, 2147465669, 2147465699, 2147465701, 2147465707, 
		2147465717, 2147465729, 2147465731, 2147465743, 2147465797, 2147465833, 
		2147465851, 2147465867, 2147465917, 2147465941, 2147465953, 2147465963, 
		2147465981, 2147465989, 2147466017, 2147466019, 2147466073, 2147466091, 
		2147466119, 2147466121, 2147466149, 2147466179, 2147466187, 2147466227, 
		2147466229, 2147466239, 2147466257, 2147466263, 2147466283, 2147466301, 
		2147466313, 2147466319, 2147466329, 2147466337, 2147466359, 2147466383, 
		2147466427, 2147466439, 2147466449, 2147466457, 2147466463, 2147466479, 
		2147466487, 2147466521, 2147466539, 2147466547, 2147466589, 2147466641, 
		2147466679, 2147466683, 2147466701, 2147466721, 2147466787, 2147466793, 
		2147466833, 2147466847, 2147466869, 2147466931, 2147466943, 2147466947, 
		2147466973, 2147466991, 2147467009, 2147467043, 2147467057, 2147467067, 
		2147467093, 2147467121, 2147467163, 2147467169, 2147467211, 2147467219, 
		2147467261, 2147467321, 2147467327, 2147467331, 2147467339, 2147467367, 
		2147467379, 2147467393, 2147467403, 2147467463, 2147467471, 2147467493, 
		2147467559, 2147467579, 2147467583, 2147467627, 2147467631, 2147467639, 
		2147467667, 2147467669, 2147467697, 2147467711, 2147467717, 2147467733, 
		2147467747, 2147467759, 2147467769, 2147467793, 2147467801, 2147467813, 
		2147467871, 2147467919, 2147467921, 2147467963, 2147467967, 2147468003, 
		2147468069, 2147468119, 2147468131, 2147468173, 2147468189, 2147468231, 
		2147468233, 2147468249, 2147468269, 2147468291, 2147468317, 2147468341, 
		2147468417, 2147468423, 2147468429, 2147468431, 2147468443, 2147468497, 
		2147468503, 2147468507, 2147468537, 2147468563, 2147468591, 2147468599, 
		2147468621, 2147468639, 2147468651, 2147468717, 2147468753, 2147468773, 
		2147468779, 2147468783, 2147468801, 2147468803, 2147468809, 2147468833, 
		2147468861, 2147468881, 2147468887, 2147468909, 2147468923, 2147468933, 
		2147468971, 2147468987, 2147468993, 2147469001, 2147469007, 2147469017, 
		2147469041, 2147469047, 2147469067, 2147469073, 2147469089, 2147469101, 
		2147469113, 2147469131, 2147469133, 2147469157, 2147469173, 2147469179, 
		2147469187, 2147469229, 2147469239, 2147469263, 2147469271, 2147469283, 
		2147469329, 2147469347, 2147469419, 2147469421, 2147469449, 2147469463, 
		2147469491, 2147469521, 2147469553, 2147469593, 2147469619, 2147469629, 
		2147469637, 2147469659, 2147469679, 2147469703, 2147469781, 2147469817, 
		2147469823, 2147469829, 2147469881, 2147469917, 2147469943, 2147469949, 
		2147469983, 2147470007, 2147470019, 2147470027, 2147470043, 2147470057, 
		2147470067, 2147470081, 2147470111, 2147470123, 2147470139, 2147470147, 
		2147470177, 2147470183, 2147470211, 2147470229, 2147470249, 2147470313, 
		2147470327, 2147470333, 2147470361, 2147470427, 2147470453, 2147470511, 
		2147470513, 2147470529, 2147470531, 2147470553, 2147470579, 2147470597, 
		2147470603, 2147470627, 2147470643, 2147470673, 2147470679, 2147470723, 
		2147470727, 2147470733, 2147470751, 2147470769, 2147470771, 2147470789, 
		2147470823, 2147470837, 2147470859, 2147470891, 2147470903, 2147470939, 
		2147470987, 2147471017, 2147471057, 2147471089, 2147471111, 2147471147, 
		2147471159, 2147471173, 2147471177, 2147471197, 2147471233, 2147471237, 
		2147471243, 2147471251, 2147471273, 2147471303, 2147471327, 2147471351, 
		2147471387, 2147471419, 2147471519, 2147471539, 2147471581, 2147471597, 
		2147471611, 2147471621, 2147471629, 2147471639, 2147471647, 2147471681, 
		2147471687, 2147471707, 2147471741, 2147471759, 2147471831, 2147471839, 
		2147471863, 2147471881, 2147471891, 2147471933, 2147471939, 2147471951, 
		2147471993, 2147472023, 2147472037, 2147472043, 2147472053, 2147472071, 
		2147472091, 2147472101, 2147472109, 2147472133, 2147472137, 2147472143, 
		2147472161, 2147472199, 2147472221, 2147472251, 2147472259, 2147472263, 
		2147472289, 2147472311, 2147472343, 2147472373, 2147472377, 2147472413, 
		2147472421, 2147472443, 2147472449, 2147472469, 2147472491, 2147472499, 
		2147472557, 2147472601, 2147472611, 2147472617, 2147472659, 2147472683, 
		2147472689, 2147472697, 2147472713, 2147472751, 2147472757, 2147472787, 
		2147472797, 2147472863, 2147472883, 2147472893,
	);
	
	function __construct($password) {
		$main_key = hash("sha512", $password);
		
		$this->generators = array();
		
		for($i = 0 ; $i < 6 ; $i++) {
			$a = 1+hexdec(substr($main_key, 0, 6)); $main_key = substr($main_key, 6);
			$x =   hexdec(substr($main_key, 0, 6)); $main_key = substr($main_key, 6);
			$c =   hexdec(substr($main_key, 0, 6)); $main_key = substr($main_key, 6);
			$m = $this->modulos[$i * 256 + hexdec(substr($main_key, 0, 2))%256]; $main_key = substr($main_key, 2);
			$this->generators[] = new LCG($a, $x, $c, $m);
		}
		
		$a = 1+hexdec(substr($main_key, 0, 2)); $main_key = substr($main_key, 2);
		$x =   hexdec(substr($main_key, 0, 2)); $main_key = substr($main_key, 2);
		$c =   hexdec(substr($main_key, 0, 2)); $main_key = substr($main_key, 2);
		$m = $this->modulos[6 * 256 + hexdec(substr($main_key, 0, 2))]; $main_key = substr($main_key, 2);
		$this->generators[] = new LCG($a, $x, $c, $m);
		
		$this->start = true;
		
		$this->iv_len = 16;
		$this->iv = file_get_contents("/dev/urandom", false, null, -1, $this->iv_len);
		assert($this->iv !== false);
		
		$this->p1 = $this->genRandChar();
		$this->p2 = $this->genRandChar();
	}
	
	function genRand() {
		$val = 0;
		for($i = 0 ; $i < count($this->generators) ; $i++) {
			$val += $this->generators[$i]->gen();
		}
		return($val);
	}
	
	function genRandChar() {
		$val = 0;
		for($i = 0 ; $i < count($this->generators) ; $i++) {
			$val += $this->generators[$i]->gen();
		}
		return($val & 0xFF);
	}
	
	function cipher($in) {
		$out = '';
		
		if($this->start) {
			$in = $this->iv.$in;
			$this->start = false;
		}
		
		for($i = 0, $l = strlen($in) ; $i < $l ; $i++) {
			$c = ord($in[$i]);
			$r = $this->genRandChar();
			$e = $c ^ $this->p1 ^ $this->p2 ^ $r;
			$out .= chr($e);
			
			$this->p1 = $c;
			$this->p2 = ($c + $e - $r) & 0xFF;
		}
		
		return($out);
	}
	
	function decipher($in) {
		$out = '';
		
		for($i = 0, $l = strlen($in) ; $i < $l ; $i++) {
			$c = ord($in[$i]);
			$r = $this->genRandChar();
			$e = $c ^ $this->p1 ^ $this->p2 ^ $r;
			$out .= chr($e);
			
			$this->p2 = $e;
			$this->p1 = ($e + $c - $r) & 0xFF;
		}
		
		if($this->start) {
			$out = substr($out, $this->iv_len);
			$this->start = false;
		}
		
		return($out);
	}
}
