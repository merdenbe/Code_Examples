// chained.cpp: Separate Chaining Map

#include "map.h"
#include <vector>
#include <stdexcept>
#include <iostream>
// Methods --------------------------------------------------------------------

                ChainedMap::ChainedMap() {

                // Initializations 
                tsize           = 0;
                load_factor     = DEFAULT_LOAD_FACTOR;
                table           = nullptr;
                num_items       = 0;

                // Set Initial Table Size
                resize(DEFAULT_TABLE_SIZE);
}

                ChainedMap::ChainedMap(float lf, const size_t s) {
                
                // Initializations 
                tsize           = 0;
                load_factor     = lf;
                table           = nullptr;
                num_items       = 0;
                    
                // Set Initial Table Size
                resize(s); 
}

                ChainedMap::~ChainedMap() { delete [] table; }

void            ChainedMap::insert(const std::string &key, const std::string &value) {
                
   
                 // Find Bucket
                size_t bucket = StringHasher{}(key) % tsize;
    
                // Insert Value 
                if (search(key) == NONE) {
                    table[bucket][key] = value;
                    num_items++;
                    if ( ((float)num_items/(float)tsize) > load_factor ){
                        resize(tsize*2);
                    }
                }
                else {
                    table[bucket][key] = value;
                }
}

const Entry     ChainedMap::search(const std::string &key) {
                // Find Bucket 
                size_t bucket = StringHasher{}(key) % tsize;

                // Search Bucket
                auto it = table[bucket].find(key);
                if (it != table[bucket].end())
                   return *it;
                else
                   return NONE; 
}

void            ChainedMap::dump(std::ostream &os, DumpFlag flag) {
                for (size_t i = 0; i < tsize; i++) {
                    if (!table[i].empty()) {
                        for (auto const& x : table[i]) {
                            switch (flag) {
                                case DUMP_KEY:          os << x.first  << std::endl; break;
                                case DUMP_VALUE:        os << x.second << std::endl; break;
                                case DUMP_KEY_VALUE:    os << x.first  << "\t" << x.second << std::endl; break;
                                case DUMP_VALUE_KEY:    os << x.second << "\t" << x.first  << std::endl; break;                   
                            } 
                        }     
                    }   
                }
}

void            ChainedMap::resize(const size_t new_size) {
                // Create New Table
                std::map<std::string, std::string> *newTable = new std::map<std::string, std::string> [new_size];
                std::map<std::string, std::string> *oldTable = table;
                table = newTable;
        
                // Copy Values 
                num_items = 0;
                size_t oldsize = tsize;
                tsize = new_size;
                for (size_t i = 0; i < oldsize; i++) {
                    for (auto x : oldTable[i]) {
                        insert(x.first, x.second);
                    }
                }

                // Delete Old Table
                delete [] oldTable;
}

// vim: set sts=4 sw=4 ts=8 expandtab ft=cpp:
