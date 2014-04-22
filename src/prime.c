#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdint.h>
#include <math.h>

void usage(const char *self) {
	printf("Usage: %s <from> <count>\n", self);
	exit(1);
}

int is_prime(uint64_t x) {
	uint64_t i;
	uint64_t max;
	
	if(x % 2 == 0)
		return(0);
	
	max = sqrt(x);
	for(i = 3 ; i <= max ; i += 2) {
		if(x % i == 0)
			return(0);
	}
	
	return(1);
}

int main(int argc, char **argv) {
	uint64_t from;
	uint64_t count;
	uint64_t i;
	uint64_t c;
	
	if(argc != 3)
		usage(argv[0]);
	
	sscanf(argv[1], "%llu", &from);
	sscanf(argv[2], "%llu", &count);
	
	if(from == 0 || count == 0)
		usage(argv[0]);
	
	for(i = from | 1, c = 0 ; c < count ; i += 2) {
		if(is_prime(i)) {
			printf("%llu\n", i);
			c++;
		}
	}
	
	return(0);
}
