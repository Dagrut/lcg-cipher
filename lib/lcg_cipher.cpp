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

#include "lcg_cipher.hpp"

#include <fstream>

#define REAL_RAND_SOURCE	"/dev/urandom"

static uint64_t modulos[256] = {
	2147769079, 2147708539, 2148104339, 2148379601, 2148848329, 2148793271, 
	2148744001, 2148224671, 2148436111, 2147841247, 2148771643, 2148349691, 
	2147775701, 2148328177, 2148399401, 2148305597, 2148431891, 2147974033, 
	2148677197, 2148190493, 2147660479, 2147985569, 2147600717, 2148474073, 
	2148326503, 2148807613, 2148605831, 2148247099, 2148384167, 2148354763, 
	2147490529, 2148454159, 2147919497, 2148062473, 2148050309, 2147743667, 
	2148835307, 2147756243, 2147871769, 2148822349, 2148630787, 2147802523, 
	2148051877, 2148300439, 2148157867, 2148279473, 2148028607, 2148769177, 
	2147545919, 2148010723, 2148454421, 2148693251, 2148664961, 2148119147, 
	2148725323, 2148513833, 2147887097, 2148674287, 2148231919, 2147593879, 
	2147751601, 2147679577, 2148187763, 2148088003, 2148616853, 2147886619, 
	2147961953, 2148289729, 2147805383, 2147628149, 2147897923, 2148035009, 
	2148715603, 2147775947, 2148549967, 2148294979, 2148656633, 2148077887, 
	2147972251, 2148500507, 2148502229, 2148327127, 2148164371, 2148433741, 
	2148203713, 2147640149, 2148382693, 2148387679, 2148098543, 2148886373, 
	2148015769, 2148006727, 2147765357, 2148329597, 2148390899, 2148090631, 
	2148257281, 2148561797, 2148807631, 2147532707, 2148571751, 2148768931, 
	2148101939, 2147872019, 2148603217, 2148384463, 2148071851, 2148768847, 
	2147710363, 2148401407, 2148769589, 2148235709, 2148875101, 2148808201, 
	2147870983, 2148646543, 2148538957, 2148846457, 2148045469, 2147492159, 
	2148792241, 2147505967, 2148423703, 2148710593, 2147504609, 2147773273, 
	2147676991, 2148503857, 2147831599, 2148802927, 2148369059, 2148216481, 
	2148430951, 2148287863, 2147692751, 2148585983, 2148332971, 2148246547, 
	2148419369, 2148676063, 2147796793, 2148035231, 2148214279, 2147659739, 
	2148137443, 2148155111, 2148327847, 2148698873, 2147515457, 2147821051, 
	2148661111, 2148813383, 2147698939, 2148688391, 2148747413, 2147603789, 
	2148754873, 2147487259, 2147995879, 2148740981, 2147570093, 2148426503, 
	2148111029, 2147486599, 2148011321, 2147810519, 2147729303, 2148085991, 
	2148459289, 2148736423, 2148148703, 2148396227, 2148202789, 2148016789, 
	2148590581, 2148182941, 2148147719, 2147689723, 2147619437, 2148870527, 
	2148071573, 2148161473, 2147792819, 2148204767, 2147715907, 2148434107, 
	2148673531, 2148100853, 2147807119, 2147935487, 2147521627, 2148357061, 
	2147836771, 2148855089, 2147924033, 2148259613, 2148528803, 2147899723, 
	2148725833, 2148218531, 2148689617, 2147768387, 2147587703, 2148071273, 
	2148210809, 2148457019, 2148236449, 2148759491, 2148386711, 2148201943, 
	2148357301, 2148432917, 2147490923, 2147821729, 2148522841, 2147545831, 
	2148407381, 2148267799, 2147632957, 2147546629, 2148482191, 2148404389, 
	2147528011, 2148559967, 2147592373, 2148165857, 2147604859, 2148390137, 
	2147625587, 2147950157, 2148785963, 2148774311, 2148704717, 2148640789, 
	2148284447, 2148414757, 2147939753, 2147756627, 2148402667, 2148752539, 
	2147531041, 2148683699, 2148521891, 2148442117, 2148782417, 2147763539, 
	2147908951, 2148674443, 2148069241, 2147659441, 2147611199, 2148697469, 
	2148330719, 2147534899, 2148430549, 2147812607
};

LCG::LCG(uint64_t a, uint64_t x, uint64_t c, uint64_t m) :
	a(a),
	x(x),
	c(c),
	m(m)
{}

uint64_t LCG::gen() {
	this->x = (this->a * this->x + this->c) % this->m;
	return(this->x);
}

#define BYTESTO24INT(array) \
	((array)[0] << 16 | (array)[1] << 8 | (array)[2])

LCGCipher::LCGCipher(const void *password, size_t length) {
	uint8_t shasum[64];
	uint8_t *shaptr = shasum;
	
	sha512_simple((const uint8_t*) password, length, shasum);
	
	for(int i = 0 ; i < 5 ; i++) {
		this->generators.push_back(LCG(
			1+BYTESTO24INT(shaptr+0),
			  BYTESTO24INT(shaptr+3),
			  BYTESTO24INT(shaptr+6),
			1+BYTESTO24INT(shaptr+9)
		));
		shaptr += 12;
	}
	
	this->generators.push_back(LCG(
		1+shasum[60],
		  shasum[61],
		  shasum[62],
		1+shasum[63]
	));
	
	std::fstream rnd_file;
	char rr_tmp[16];
	const int rnd_cnt = this->genRand() % 9 + 8;
	
	rnd_file.open(REAL_RAND_SOURCE, std::ios::in | std::ios::binary);
	if(!rnd_file.is_open()) {
		rnd_file.close();
		std::string error("The file " REAL_RAND_SOURCE " can not be opened!");
		throw error;
	}
	
	this->rr_data_size = rnd_cnt;
	this->rr_data = new uint8_t[rnd_cnt];
	rnd_file.get((char*) this->rr_data, rnd_cnt);
	rnd_file.close();
	
	this->is_start = true;
	
	this->p1 = this->genRandChar();
	this->p2 = this->genRandChar();
}

LCGCipher::~LCGCipher() {
	if(this->rr_data)
		delete[] rr_data;
}

uint64_t LCGCipher::genRand() {
	uint64_t ret = 0;
	
	for(int i = 0, l = this->generators.size() ; i < l ; i++) {
		ret += this->generators[i].gen();
	}
	
	return(ret);
}

void LCGCipher::cipher(const uint8_t *in, int length, std::vector<uint8_t> &out) {
	int c;
	int r;
	int e;
	
	if(this->is_start) {
		this->is_start = false;
		out.reserve(this->rr_data_size + length);
		this->cipher(this->rr_data, this->rr_data_size, out);
	}
	else {
		out.reserve(length);
	}
	
	for(int i = 0; i < length ; i++) {
		c = in[i];
		r = this->genRandChar();
		e = c ^ this->p1 ^ this->p2 ^ r;
		out.push_back(e);
		
		this->p1 = c;
		this->p2 = (c + e - r) & 0xFF;
	}
}

void LCGCipher::decipher(const uint8_t *in, int length, std::vector<uint8_t> &out) {
	int c;
	int r;
	int e;
	
	if(this->is_start) {
		std::vector<uint8_t> tmp;
		int size = (this->rr_data_size > length ? length : this->rr_data_size);
		this->is_start = false;
		this->decipher(in, size, tmp);
		if(size < this->rr_data_size) {
			this->is_start = false;
			this->rr_data_size -= size;
			return;
		}
		in += size;
		length -= size;
	}
	
	out.reserve(length);
	
	for(int i = 0; i < length ; i++) {
		c = in[i];
		r = this->genRandChar();
		e = c ^ this->p1 ^ this->p2 ^ r;
		out.push_back(e);
		
		this->p2 = e;
		this->p1 = (e + c - r) & 0xFF;
	}
}
