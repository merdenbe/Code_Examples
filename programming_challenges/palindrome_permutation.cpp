// Michael Erdenberger, Section 01 

// Challenge 08: Palindrome Permutation

#include <string>
#include <unordered_set>
#include <iostream>
#include <cctype>

using namespace std;

bool is_palindrome(string);

// Main Execution

int main(int argc, char *argv[]) {
    
    // Variable declaration
    string str1 = " is a palindrome permutation";
    string str2 = " is not a palindrome permutation";
    string tmp;

    // Checks each line if it is a palindrome, outputs accordingly
    while (getline(cin, tmp)) {
        if ( is_palindrome(tmp) )
            cout << "\"" + tmp + "\"" + str1;
        else 
            cout << "\"" + tmp + "\"" + str2;

        cout << endl;
    }

    return EXIT_SUCCESS;

}

bool is_palindrome(string s) {
    
    // Declares set 
    unordered_set<char> myset;

    // Iterates through string 
    for (auto it = s.begin(); it < s.end(); it++) {
        if ( isalpha(*it) ) {                                       // if letter isn't in set add it 
            unordered_set<char>::iterator got = myset.find(*it);
            if (got == myset.end())
                myset.insert(*it);
            else                                                    // if letter is in set, erase it 
                myset.erase(got);
        }
    }

    return (myset.size() < 2) ? true : false;                       // by the end, the size of the list is the number of chars with an odd number of occurences

}
// vim: set sts=4 sw=4 ts=8 expandtab ft=cpp:
