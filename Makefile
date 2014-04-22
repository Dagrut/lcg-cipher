CFLAGS=-O3
LDFLAGS=

all: lcgc prime

prime: src/prime.c
	mkdir -p build
	gcc $(CFLAGS) -o build/prime.o -c src/prime.c
	gcc $(LDFLAGS) -lm -o prime build/prime.o

lcgc: build/lcgc.o build/lcg_cipher.o build/sha512.o
	g++ $(LDFLAGS) -o lcgc build/lcgc.o build/lcg_cipher.o build/sha512.o

build/lcgc.o: src/lcgc.cpp lib/lcg_cipher.hpp lib/sha-2/sha512.h
	mkdir -p build
	g++ $(CFLAGS) -std=c++11 -o build/lcgc.o -c src/lcgc.cpp

build/lcg_cipher.o: lib/lcg_cipher.cpp lib/lcg_cipher.hpp lib/sha-2/sha512.h
	mkdir -p build
	g++ $(CFLAGS) -std=c++11 -o build/lcg_cipher.o -c lib/lcg_cipher.cpp

build/sha512.o: lib/sha-2/sha512.h lib/sha-2/sha512.c
	mkdir -p build
	gcc $(CFLAGS) -o build/sha512.o -c lib/sha-2/sha512.c

clean:
	rm -rf build

rebuild:
	make clean all
