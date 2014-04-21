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
