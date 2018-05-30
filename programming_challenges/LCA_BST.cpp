// Challenge 06: Lowest Common Ancestor
// Michael Erdenberger

#include <vector>
#include <iostream>
#include <algorithm>

using namespace std;

// Node Struct

struct Node {
    int     data;
    Node*   left;
    Node*   right;
};   

// BST Class

class BST {
  public:
    BST();                                                   // constructor 
    ~BST();                                                  // destructor 
    Node*   head;                                            // returns head of BST
    void    insert(int);                                     // inserts new node into list
    void    deleteNode(Node*);                               // called by destructor to recursively delete nodes 
    int     LCA(int, int);                                   // returns LCA of two nodes 
};

BST::BST() 
    { head = nullptr; }

BST::~BST() 
    {  deleteNode(head);  }                                  // Calls recursive delete function   


void BST::deleteNode(Node* n) {
    if (n != nullptr) {
        // Save pointers to left and right children
        Node* leftChild   = n->left;
        Node* rightChild  = n->right;
    
        // Delete node 
        delete n;
    
        // Recursive calls on left and right children
        if (leftChild != nullptr)
            deleteNode(leftChild);
        if (rightChild !=nullptr)
            deleteNode(rightChild);
    }
}

void BST::insert(int value) {
    
    // Declare new node and store value in it 
    Node* newNode    = new Node;
    newNode->left    = nullptr;
    newNode->right   = nullptr;
    newNode->data    = value;   

    // Find position on the tree and attach node 
    Node* curr = head;
    bool stop = true;
    while (stop) {
        if (head == nullptr) {
            stop = false;
            head = newNode;
        }
        else if (value > curr->data && curr->right == nullptr) {
            stop = false;
            curr->right = newNode;
        }    
        else if (value <= curr->data && curr->left == nullptr) {
            stop = false;
            curr->left = newNode;
        }
        else if (value > curr->data)
            curr = curr->right;
        else
            curr = curr->left;
        
    }
}

int BST::LCA(int val1, int val2) {
    
    // Initialize curr pointer and pivot value 
    Node* curr = head;
    int pivot = head->data;

    // Continue while both values are on one side of the pivot
    while ( ((val1 < pivot) && (val2 < pivot)) || ((val1 > pivot) && (val2 > pivot)) ) {
        if (val1 > pivot) 
            curr = curr->right;
        else 
            curr = curr->left;
        pivot = curr->data;
    }
    
    // By the end, the pivot is the LCA
    return pivot; 
}

     
// Main Execution

int main(int argc, char *argv[]) {
    
    // Initial Declaration
    int N, tmp, numPairs, val1, val2;
    cin >> N;
    
    // Reading input and delivering proper output
    while (N != -1) {
        BST tree;
        for (int i = 0; i < N; i++) {
            cin >> tmp;
            tree.insert(tmp);
        }
        cin >> numPairs;
        for (int j = 0; j < numPairs; j++) {
            cin >> val1 >> val2;
            cout << tree.LCA(val1, val2) << endl;
        }
        cin >> N;
        if (N != -1)
            cout << endl;   
    } 
    return EXIT_SUCCESS;
}

// vim: set sts=4 sw=4 ts=8 expandtab ft=cpp:
