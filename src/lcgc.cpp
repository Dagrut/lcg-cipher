#include "../lib/lcg_cipher.hpp"

#include <cstdio>
#include <cstring>

#define BUFFER_SIZE 1024

void usage(const char *self, const std::string &error = std::string()) {
	if(error.size() > 0)
		printf("Error: %s\n", error.c_str());
	fprintf(stderr, "Usages : %s cipher|decipher <password> [input file [output file]]\n", self);
	fprintf(stderr, "         %s prng <password> <count>\n", self);
	fprintf(stderr, "         %s debug <password>\n", self);
	exit(error.size() == 0 ? 0 : 1);
}

void do_cipher_decipher(int argc, char **argv) {
	LCGCipher *lc;
	uint8_t *rbuffer;
	size_t rbuffer_len;
	std::vector<uint8_t> wbuffer;
	FILE *in_fp = NULL;
	FILE *out_fp = NULL;
	std::string password;
	
	password = argv[2];
	
	if(argc >= 4) {
		if(strcmp(argv[3], "-") != 0) {
			in_fp = fopen(argv[3], "rb");
			if(in_fp == NULL)
				usage(argv[0], std::string("Cannot open ") + argv[3] + " for reading!");
		}
	}
	if(argc == 5) {
		if(strcmp(argv[4], "-") != 0) {
			out_fp = fopen(argv[4], "wb");
			if(out_fp == NULL)
				usage(argv[0], std::string("Cannot open ") + argv[4] + " for writing!");
			rewind(out_fp);
		}
	}
	
	if(in_fp == NULL)
		in_fp = stdin;
	if(out_fp == NULL)
		out_fp = stdout;
	
	try {
		lc = new LCGCipher(password.c_str(), password.size());
	}
	catch(std::string &e) {
		printf("LCGCipher error : '%s'\n", e.c_str());
		exit(1);
	}
	rbuffer = new uint8_t[BUFFER_SIZE];
	
	if(strcmp(argv[1], "cipher") == 0) {
		while((rbuffer_len = fread(rbuffer, sizeof(rbuffer[0]), BUFFER_SIZE, in_fp)) > 0) {
			lc->cipher(rbuffer, rbuffer_len, wbuffer);
			fwrite(wbuffer.data(), sizeof(rbuffer[0]), wbuffer.size(), out_fp);
			wbuffer.clear();
		}
	}
	else {
		while((rbuffer_len = fread(rbuffer, sizeof(rbuffer[0]), BUFFER_SIZE, in_fp)) > 0) {
			lc->decipher(rbuffer, rbuffer_len, wbuffer);
			fwrite(wbuffer.data(), sizeof(rbuffer[0]), wbuffer.size(), out_fp);
			wbuffer.clear();
		}
	}
	
	delete lc;
	delete[] rbuffer;
	if(in_fp != stdin)
		fclose(in_fp);
	if(out_fp != stdout)
		fclose(out_fp);
}

void do_prng(int argc, char **argv) {
	LCGCipher *lc;
	std::string password;
	uint64_t count;
	
	password = argv[2];
	
	try {
		lc = new LCGCipher(password.c_str(), password.size());
	}
	catch(std::string &e) {
		printf("LCGCipher error : '%s'\n", e.c_str());
		exit(1);
	}
	
	sscanf(argv[3], "%llu", &count);
	
	for(uint64_t i = 0 ; i < count ; i++) {
		printf("%llu\n", lc->genRand());
	}
	
	delete lc;
}

void do_debug(int argc, char **argv) {
	LCGCipher *lc;
	std::string password;
	
	password = argv[2];
	
	try {
		lc = new LCGCipher(password.c_str(), password.size());
	}
	catch(std::string &e) {
		printf("LCGCipher error : '%s'\n", e.c_str());
		exit(1);
	}
	
	printf("a         x         c         m\n");
	
	for(int i = 0, l = lc->generators.size() ; i < l ; i++) {
		printf(
			"%-9llu %-9llu %-9llu %-9llu\n",
			lc->generators[i].a,
			lc->generators[i].x,
			lc->generators[i].c,
			lc->generators[i].m
		);
	}
	
	delete lc;
}

int main(int argc, char **argv) {
	if(argc < 2)
		usage(argv[0]);
	
	if(strcmp(argv[1], "cipher") == 0 || strcmp(argv[1], "decipher") == 0) {
		if(argc < 3 || argc > 5)
			usage(argv[0]);
		
		do_cipher_decipher(argc, argv);
	}
	else if(strcmp(argv[1], "prng") == 0) {
		if(argc != 4)
			usage(argv[0]);
		
		do_prng(argc, argv);
	}
	else if(strcmp(argv[1], "debug") == 0) {
		if(argc != 3)
			usage(argv[0]);
		
		do_debug(argc, argv);
	}
	else {
		usage(argv[0], std::string("Unknown command ") + argv[1]);
	}
	
	return(0);
}
