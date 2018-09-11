/* 	Michael Erdenberger
	Programming Challenges
	Challenge00 			*/

#include <iostream>
#include <string>
using namespace std;

bool isTidy(int);

int main() {
	
	// Variable Declaration
	int totalDecks = 0;
	int totalCards = 0;

	cin >> totalDecks;
	for (int i = 0; i < totalDecks; i++) {
		cin >> totalCards;
		for (int j = totalCards; j > 0; j--) {
			if (isTidy(j)) {
				cout << "Deck #" << (i+1) << ": " << j << "\n";
				break;
			}
		}
	}

	return 0;

}

// Checks int if it's tidy 
bool isTidy(int x) {
	string number = to_string(x); 
	if (number.length() == 1) {
		return true; // check to avoid loop
	}
	for (int i = 1; i < number.length(); i++) {
		if ((int)number[i-1] > (int)number[i]) {
			return false;
		}
	}
	return true;
}
