// Challenge 05: BST
// Michael Erdenberger

#include <iostream>
#include <vector>

using namespace std;

bool checkBST(vector<int> &);

// Main Execution

int main(int argc, char *argv[]) {
   
    // Inital Declarations  
    int N, tmp, count = 1;
    vector<int> bTree;
    
    // Read in data and continue while there is stdin
    while (cin >> N) {
        
        for (int i = 0; i < N; i++) {                           // push nodes into vector
            cin >> tmp;
            bTree.push_back(tmp);
        }

        if (checkBST(bTree))                                    // if the bTree is a BST, output the corresponding statement
            cout << "Tree " << count << " is a BST\n";
        else
            cout << "Tree " << count << " is not a BST\n";
        
        bTree.clear();                                          // clear vector
        count++;                                                // increment count 
        
    }    
    return EXIT_SUCCESS;
}


// Check for BST conditions 
bool checkBST(vector<int> &vec) {
 
    // Initial Declarations
    int size = vec.size(); 
    int limit = (size-2)/2;
    int parent, leftChild, rightChild;

    for (int i = 0; i <= limit; i++) {
        if (vec[i] != -1) {                                     // checks if node exists 
            parent = vec[i];
            leftChild = vec[2*i+1];
            if (leftChild != -1 && parent < leftChild)          // checks is left child follows condition otherwise returns false
                return false;
            if ((size-1) >= (2*i+2)) {                          // if right child is within the vector 
                rightChild = vec[2*i+2];                        
                if (rightChild != -1 && parent >= rightChild)   // if right child follows the condition otherwise return false
                    return false;
            }
        }
            
    }
    
    return true;                                                // if it makes it through the vector with all conditions met, return true

} 

// vim: set sts=4 sw=4 ts=8 expandtab ft=cpp:
