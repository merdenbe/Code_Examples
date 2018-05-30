// Michael Erdenberger, Section 01
// challenge02 solution

#include <iostream>
#include <string>
using namespace std;


// List Class

// Declaration 
template <typename T>
class List {
	protected:
		struct Node {			// every item in the list is a node
		   T	Data; 			// contains data of type T 
		   Node *Next; 			// contains pointer to next value 
		};
	
		typedef Node * iterator; 	// has an iterator that can move through the list 

		Node *head;			// head pointer
		size_t length; 			// length (always positive)

	public:
		List() : head(nullptr), length(0) {} 		// constructor
		iterator front() { return head; };		// returns pointer to first node
		
		~List();					// destructor
		List(const List<T> &other);			// copy constructor
		List<T>& operator=(List<T> other);		// assignment operator
		void swap(List<T> &other); 			//swap function

		size_t size() const { return length; } 		// returns length of list
		T& at(const size_t i);				// returns Data at node position i
		void insert(iterator it, const T &data);	// inserts new node at iterator position
		void push_back(const T &data);			// adds value to end of list
		void erase(iterator it);			// erases last vale 
};

// Implementation
template <typename T>
List<T>::~List() {
	Node *next = nullptr;

	for (Node *curr = head; curr != nullptr; curr = next) {			// deletes each node of list
		next = curr->Next; 
		delete curr;
	}
}

template <typename T>
List<T>::List(const List<T> &other)
	: head(nullptr), length(0) {
	for (Node *curr = other.head; curr != nullptr; curr = curr->Next) {	// copies each node 
		push_back(curr->Data);
	}
}

template <typename T>
List<T>& List<T>::operator=(List<T> other) {
	swap(other);
	return *this;
}

template <typename T>
void List<T>::swap(List<T> &other) {
	std::swap(head, other.head);
	std::swap(length, other.length);
}

template <typename T>
T& List<T>::at(const size_t i) {
	Node *node = head;
	size_t n = 0;
	
	while (n < i && node != nullptr) {					// traverses list to node of interest
		node = node->Next;			
		n++;
	}

	if (node) {
		return node->Data;						// returns data
	} 
}

template <typename T>
void List<T>::insert(iterator it, const T& data) {
	if (head == nullptr && it == nullptr) {
		head = new Node{data, nullptr};
	}									
	else {
		Node *node = new Node{data, it->next};				// traverses to node and then changes pointers 
		it->next = node;
	}
	length++;
}

template <typename T>
void List<T>::push_back(const T& data) {
	if (head == nullptr) {
		head = new Node{data, nullptr};
	}
	else {									// traverses to end of the list, inserts behind 
		Node *curr = head;
		Node *tail = head;
		
		while (curr) {
			tail = curr;
			curr = curr->Next;
		}

		tail->Next = new Node{data, nullptr};
	}
	length++;
}

template <typename T>
void List<T>::erase(iterator it ) {
	if (head == it) {
		head = head->Next;
		delete it;
	}
	else {
		Node *node = head;
		
		while (node != nullptr && node->next != it) {
			node = node->Next;
		}

		node->Next = it->Next;
		delete it;
	}
	length--;
}

//------------------------------------------------------------------------------------------------------

// Function Prototype 

List<int> add(List<int>, List<int>);
void printList(List<int>);

// Main Execution

int main(int argc, char *argv[]) {
	string integer1, integer2;					// variable initiailization
	
	while (cin >> integer1 >> integer2) {
		// padding variables
		int diff = integer1.length() - integer2.length();
		if (diff > 0) {
			for (int i = 0; i < diff; i++) {
				integer2.insert(0, "0");		 // pads smaller number to be the same size with leading zeros 
			}
		}
		else if (diff < 0) {
			for (int i = 0; i < (-diff); i++) {
				integer1.insert(0, "0"); 
			}
		}
		integer1.insert(0, "0");				 // pads additional zero to both in case of carry
		integer2.insert(0, "0");
	
		// Loading strings into lists
		List<int> int1, int2, sum;
		for (int i = integer1.length(); i > 0; i--) {
			int1.push_back((integer1[i-1])-'0');
			int2.push_back((integer2[i-1])-'0');
		}
		
		sum = add(int1, int2);
		printList(sum);
		cout << endl; 
	}
	return 0;
}

// Adds two lists and returns a list
List<int> add(List<int> a, List<int> b) {
	List<int> sum;
	int carry = 0;
	int tmp = 0;
	for (size_t i = 0; i < a.size(); i++) {
		tmp = a.at(i) + b.at(i) + carry;
		sum.push_back(tmp%10);			 // ensures it does not store a value over ten 
		if (tmp >= 10) { 
			carry = 1;			 // sets correct carry value 
		}
		else {
			carry = 0;
		}
	}
	return sum;
}

// Prints out the list in the correct format to match output
void printList(List<int> list) {
	for (size_t j = list.size(); j > 0; j--) {
		if (j == list.size() && list.at(j-1) == 0) {	// if theres a leading 0, don't print it out 
		;							
		}
		else {
			cout << list.at(j-1);			// outputs each vale of the list 
		}
	}
}








	
