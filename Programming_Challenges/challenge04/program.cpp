#include <iostream>
#include <vector>
#include <algorithm>
#include <sstream>
#include <string>

using namespace std;

bool test_arrangement(vector<int> &, vector<int> &);

int main(int argc, char *argv[]) {

	string goldLine, blueLine;

    while (getline(cin, goldLine) && getline(cin, blueLine)) {
   
		vector<int> blue, gold; 
        istringstream iss_g(goldLine);
		istringstream iss_b(blueLine);
        for ( string goldLine; iss_g >> goldLine; ) { 
            gold.push_back(stoi(goldLine));
        }
		for ( string blueLine; iss_b >> blueLine; ) {
			blue.push_back(stoi(blueLine));
		}

		sort(gold.begin(), gold.end());
		sort(blue.begin(), blue.end());
		
		cout << (test_arrangement(gold, blue) ? "Yes\n" : "No\n");	
	
    }   
	
	return 0;
}

bool test_arrangement(vector<int> &g, vector<int> &b) {
	for (int i = g.size()-1; i >= 0; i--) {
		if (g[i] >= b[i]) {
			return false;
		}
	}
	return true;
}
