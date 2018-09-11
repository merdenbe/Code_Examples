/* Michael Erdenberger
   Programming Challenges 
   Challenge01			 */

#include <iostream>
#include <string>
#include <map>

using namespace std;

int main() {

	string s1;
	string s2;

	while (cin >> s1 >> s2) {
		
		map<char, int> s1_map;
		for (auto it = s1.begin(); it < s1.end(); it++) {
			auto s1_it = s1_map.find(*it);
			if (s1_it == s1_map.end()) {
				s1_map[(*it)] = 1;
			} else {
				s1_map[(*it)]++;
			}
		}

		map<char, int> s2_map;
		for (auto it = s2.begin(); it < s2.end(); it++) {
			auto s2_it = s2_map.find(*it);
			if (s2_it == s2_map.end()) {
				s2_map[(*it)] = 1;
			} else {
				s2_map[(*it)]++;
			}
		}	
		
		int minInstances = 1000;
		for (auto const& letter : s2_map) {
			int currInstances = s1_map[letter.first]/letter.second;
			if (currInstances < minInstances) {
				minInstances = currInstances;
			}
		}
	
		cout << minInstances << "\n";
	}
	return 0;
}
