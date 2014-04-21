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

#ifndef _H_LCG_CIPHER
#define _H_LCG_CIPHER

extern "C" {
	#include "sha-2/sha512.h"
}

#include <string>
#include <vector>
#include <cstdint>

struct LCG {
	LCG(uint64_t a, uint64_t x, uint64_t c, uint64_t m);
	
	uint64_t gen();
	
	uint64_t a;
	uint64_t x;
	uint64_t c;
	uint64_t m;
};

struct LCGCipher {
	LCGCipher(const void *password, size_t length);
	~LCGCipher();
	
	uint64_t genRand();
	inline uint8_t genRandChar() {
		return(this->genRand() & 0xFF);
	}
	
	void cipher(const uint8_t *in, int length, std::vector<uint8_t> &out);
	void decipher(const uint8_t *in, int length, std::vector<uint8_t> &out);
	
	std::vector<LCG> generators;
	uint8_t *rr_data;
	size_t rr_data_size;
	int is_start;
	
	int p1;
	int p2;
};

#endif
