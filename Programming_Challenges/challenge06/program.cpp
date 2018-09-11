#include <iostream>
#include <set>
#include <map>
#include <vector>
#include <sstream>
#include <string>

using namespace std;

vector<string> get_distinct(vector<string>);

int main(int argc, char *argv[]) {
	
	string line;
	while (getline(cin, line)) {
		
		stringstream iss(line);
		vector<string> fruits;
		for ( string line; iss >> line; ) { 
			fruits.push_back(line);
        }
		
		vector<string> distinct = get_distinct(fruits);
		cout << distinct.size() << ": ";
		for (auto it = distinct.begin(); it != distinct.end(); it++) {
			cout << (*it) << (it != distinct.end()-1 ? ", " : "\n");
		}
	}

	return 0;
}

vector<string> get_distinct(vector<string> fruits) {	
	vector<string> distinct;
	vector<vector<string>> possibleSeq;
	map<string, int> lastIndex;
	for (int i = 0; i < fruits.size(); i++) {
		string fruit = fruits[i];
		if (lastIndex.count(fruit) == 0) {
			lastIndex[fruit] = i;
			distinct.push_back(fruit);
		} else {
			possibleSeq.push_back(distinct);
			i = lastIndex[fruit];
			distinct.clear();
			lastIndex.clear();
		}
	}
	possibleSeq.push_back(distinct);
	distinct = possibleSeq[0];
	for (auto vec : possibleSeq) {
		if (vec.size() > distinct.size()) {
			distinct = vec;
		}
	}
	return distinct;
}
