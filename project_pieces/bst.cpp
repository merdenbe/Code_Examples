// bst.cpp: BST Map

#include "map.h"

#include <stdexcept>

// Prototypes ------------------------------------------------------------------

Node *insert_r(Node *node, const std::string &key, const std::string &value);
Node *search_r(Node *node, const std::string &key);
void    dump_r(Node *node, std::ostream &os, DumpFlag flag);
void    deleteNodes_r(Node*);

// Methods ---------------------------------------------------------------------
                BSTMap::BSTMap() { root = nullptr; }

                BSTMap::~BSTMap() { deleteNodes_r(root); }

void            BSTMap::insert(const std::string &key, const std::string &value) {
     if (root == nullptr) 
        root = insert_r(root, key, value);                                          // if tree is empty sets new root in addition to insert
    else if ( search(key) == NONE)   
        insert_r(root, key, value);                                                 // normal insert
    else {
        Node* target = search_r(root, key);                                         // if key is already in the tree, it overwrites the value 
        target->entry.second = value;
    }
}

const Entry     BSTMap::search(const std::string &key) {
    Node* target = search_r(root, key);
    if (target != nullptr)                                                          // if key is found in the tree returns the node 
        return target->entry;
    else 
        return NONE;                                                                // if the key is not found, it returns NONE
}

void            BSTMap::dump(std::ostream &os, DumpFlag flag) {
    dump_r(root, os, flag); 
}

// Internal Functions ----------------------------------------------------------

Node *insert_r(Node *node, const std::string &key, const std::string &value) {
   
    Node *target = nullptr;

    if (node == nullptr) {                                                          // if the root it nullptr, creates node and sets root
        Node* newNode = new Node { Entry(key,value), 0, nullptr, nullptr };               
        return newNode;
    }
    else if ( (node->entry.first).compare(key) < 0 && node->left == nullptr ) {      // if key should be placed to the left and left is open, places new node to left
        Node* newNode = new Node { Entry(key,value), 0, nullptr, nullptr };
        node->left = newNode;
        return newNode;
    }
    else if ( (node->entry.first).compare(key) > 0 && node->right == nullptr ) {     // if key should be placed to the right and right is open, placed new node to right
        Node* newNode = new Node { Entry(key,value), 0, nullptr, nullptr };
        node->right = newNode;
        return newNode;
    }
    else if ( (node->entry.first).compare(key) < 0 ) {                                // if key should be placed to the left and left is not open, searches left subtree
        target = insert_r(node->left, key, value);
    }
    else {
        target = insert_r(node->right, key, value);                                 // if key should be placed to the right and right is not open, searches right subtree
    }
    
    return target;                                                                  // returns a pointer to the newly created node 

}

Node *search_r(Node *node, const std::string &key) {
    
    Node *target = nullptr;

    if (node == nullptr || (node->entry.first).compare(key) == 0 ) {                  // if empty tree or found, return the node 
        return node;
    }   
    else if ( (node->entry.first).compare(key) < 0 && node->left != nullptr ) {      // search left subtree
        target = search_r(node->left, key);
    }
    else if ( (node->entry.first).compare(key) > 0 && node->right != nullptr ) {     // search right subtree
        target = search_r(node->right, key);
    }
    return target;
}

void dump_r(Node *node, std::ostream &os, DumpFlag flag) {
    // in-order traversal

    if (node == nullptr) {
        return;
    }

    if (node->left != nullptr) {
        dump_r(node->left, os, flag);
    }

    switch (flag) {
        case DUMP_KEY:          os << node->entry.first  << std::endl; break;
        case DUMP_VALUE:        os << node->entry.second << std::endl; break;
        case DUMP_KEY_VALUE:    os << node->entry.first  << "\t" << node->entry.second << std::endl; break;
        case DUMP_VALUE_KEY:    os << node->entry.second << "\t" << node->entry.first  << std::endl; break;
    }   

    if (node->right != nullptr) {
        dump_r(node->right, os, flag);
    }
}

void deleteNodes_r(Node* n) {
        if (n != nullptr) {
            // Save pointers to left and right children
            Node* leftChild   = n->left;
            Node* rightChild  = n->right;
                     
            // Delete node 
            delete n;
                                                             
            // Recursive calls on left and right children
            if (leftChild != nullptr) 
                deleteNodes_r(leftChild);
            if (rightChild !=nullptr)
                deleteNodes_r(rightChild);
      }
} 
// vim: set sts=4 sw=4 ts=8 expandtab ft=cpp:
