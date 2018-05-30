#!/usr/bin/env python3

import functools
import hashlib
import itertools
import multiprocessing
import os
import string
import sys

# Constants

ALPHABET    = string.ascii_lowercase + string.digits
ARGUMENTS   = sys.argv[1:]
CORES       = 1
HASHES      = 'hashes.txt'
LENGTH      = 1
PREFIX      = ''

# Functions

def usage(exit_code=0):
    print('''Usage: {} [-a alphabet -c CORES -l LENGTH -p PREFIX -s HASHES]
    -a ALPHABET Alphabet to use in permutations
    -c CORES    CPU Cores to use
    -l LENGTH   Length of permutations
    -p PREFIX   Prefix for all permutations
    -s HASHES   Path of hashes file'''.format(os.path.basename(sys.argv[0])))
    sys.exit(exit_code)

def sha1sum(s):
    return hashlib.sha1(s.encode()).hexdigest()                                                              # returns the string of the sha1 encoding

def permutations(length, alphabet=ALPHABET):                                                                # recursively calls itself building the permutation on returning
    if length == 1:
        for letter in alphabet:
            yield letter
    else: 
        for letter in alphabet:
            for perm in permutations(length-1, alphabet):
                yield letter+perm

def smash(hashes, length, alphabet=ALPHABET, prefix=''):
    permutationList = [ prefix+perm for perm in permutations(length, alphabet)  ]
    return [perm for perm in permutationList if sha1sum(perm) in hashes ]                                       

# Main Execution

if __name__ == '__main__':
    # Parse command line arguments
    args = sys.argv[1:]
    while len(args) and args[0].startswith('-') and len(args[0]) > 1:
            arg = args.pop(0)
            if arg == '-a':
                ALPHABET = args.pop(0)
            elif arg == '-c':
                CORES = int(args.pop(0))
            elif arg == '-l':
                LENGTH = int(args.pop(0))
            elif arg == '-p':
                PREFIX = args.pop(0)
            elif arg == '-s':
                HASHES = args.pop(0)
            elif arg == '-h':
                usage(0)
            else:
                usage(1)

    # Load hashes set                                                                           
    hashesSet = set()                                                                                       # creates empty set of hashes 
    for line in open(HASHES):
        hashesSet.add(line.strip())                                                                         # adds each stripped hash to the set 
    # Execute smash function
    if CORES > 1 and LENGTH > 1:                                                                            # correctly calls smash function based on length and cores 
        prefixes = [ PREFIX + letter for letter in ALPHABET ] 
        pool = multiprocessing.Pool(CORES)
        subsmash = functools.partial(smash, hashesSet, (LENGTH - 1), ALPHABET)
        passwords = itertools.chain.from_iterable(pool.imap(subsmash, prefixes))
    else:
        passwords = smash(hashesSet, LENGTH, ALPHABET, PREFIX)
    # Print passwords
    for password in passwords:
        print(password)
# vim: set sts=4 sw=4 ts=8 expandtab ft=python:
