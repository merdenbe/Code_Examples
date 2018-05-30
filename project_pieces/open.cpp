// open.cpp: Open Addressing Map

#include "map.h"

#include <stdexcept>

// Methods --------------------------------------------------------------------

                OpenMap::OpenMap() {

                // Initializations
                tsize               = 0;
                load_factor         = DEFAULT_LOAD_FACTOR;
                table               = nullptr;
                num_items           = 0;

                // Set Inital Table Size
                resize(DEFAULT_TABLE_SIZE);
}

                OpenMap::OpenMap(float lf, const size_t s) {

                // Initializations
                tsize               = 0;
                load_factor         = lf;
                table               = nullptr;
                num_items           = 0; 

                // Set Initial Table Size 
                resize(s);
}

                OpenMap::~OpenMap() { delete [] table; }


void            OpenMap::insert(const std::string &key, const std::string &value) {
                
                // Locate Bucket 
                size_t bucket           = locate(key);
               
                // Change Values  
                table[bucket].first     = key;
                table[bucket].second    = value;
}

const Entry     OpenMap::search(const std::string &key) {

                // Locate Bucket 
                size_t bucket  = locate(key);

                // Return value at bucket
                return table[bucket]; 
}

void            OpenMap::dump(std::ostream &os, DumpFlag flag) {
                
                for (size_t i = 0; i < tsize; i++) {
                    if (table[i] != NONE) {
                        switch (flag) {
                            case DUMP_KEY:          os << table[i].first  << std::endl; break;
                            case DUMP_VALUE:        os << table[i].second << std::endl; break;
                            case DUMP_KEY_VALUE:    os << table[i].first  << "\t" << table[i].second << std::endl; break;
                            case DUMP_VALUE_KEY:    os << table[i].second << "\t" << table[i].first  << std::endl; break;
                        }
                    }
                }

}




size_t          OpenMap::locate(const std::string &key) {
                
                // Hash Key
                size_t bucketStart = StringHasher{}(key) % tsize;
                size_t bucket      = bucketStart;

                // Trivial Case
                if (table[bucketStart].first == key || table[bucketStart] == NONE)
                    return bucket;
                
                // Linear Probing
                bucket = (bucket + 1) % tsize;
                while (table[bucket].first != key && table[bucket] != NONE && bucket != bucketStart) {
                    bucket = (bucket + 1) % tsize;        
                }
                if (bucket == bucketStart) {
                    resize(tsize*2);                // if full loop is made 
                    return locate(key);             // recursive call to get empty bucket on resized table
                }
                else 
                    return bucket;
}

void            OpenMap::resize(const size_t new_size) {

                // Create New Table
                Entry *newTable = new Entry [new_size];
                Entry *oldTable = table;
                table = newTable;
                
                // Initialize Entries in Array 
                for (size_t i = 0; i < new_size; i++) 
                    table[i] = NONE;
                
                // Copy Values
                num_items = 0;
                size_t oldsize = tsize;
                tsize = new_size;
                for (size_t i = 0; i < oldsize; i++) 
                    insert(oldTable[i].first, oldTable[i].second);

                // Delete Old Table 
                delete [] oldTable;
}

// vim: set sts=4 sw=4 ts=8 expandtab ft=cpp:
