// Challenge 01: Rotating Arrays

// Main Execution
#include <iostream>
#include <vector>
using namespace std;

void shiftLeft(vector<int> &, int);
void shiftRight(vector<int> &, int, int);

int main(int argc, char *argv[]) {
    // Read in data 
    int n, r, temp;
    char d;
    while (cin >> n >> r >> d) { // continue while htere is still data
        // Create original vector
        vector<int> v (n, 0);
        for (int i = 0; i < n; i++) {
            cin >> temp;
            v[i] = temp;
         }
     
        // Shift vector either left or right r times
        if (d == 'L') 
             shiftLeft(v, r);
        else 
             shiftRight(v, n, r);

        // Output Vector to match output file
        for (int i = 0; i < n; i++) {
             if (i < (n-1))
                cout << v[i] << ' ';
            else
                cout << v[i];
        }
        cout << endl;        
    }
    return 0;
}

void shiftLeft(vector<int> &v, int r) {
   for (int i = 0; i < r; i++) { // done r times
         v.push_back(v[0]); // adds first value in the vector to the end of the vector
         v.erase(v.begin()); // erases the first value of the vector 
    }
}

void shiftRight(vector<int> &v, int n, int r) { // done r times
   for (int i = 0; i < r; i++) {
        v.insert(v.begin(), v[n-1]); // inserts last value of vector to the beginning of the vector
        v.pop_back(); // erases the last value of the vector 
   }
}
        
// vim: set sts=4 sw=4 ts=8 expandtab ft=cpp:
