
Ever changing company hierarchy Introduction:

Help HR manager The Company get a grasp of her ever changing company’s hierarchy! Every week The Company receives a JSON of employees and their supervisors from her demanding CEO Chris, who keeps changing his mind about how to structure his company. Chris wants this JSON to be restructured, so that he can better see the employees hierarchy. Chris is especially happy if The Company provides him with a visual graphical representation of the hierarchy.

To make her life easier, The Company wants to have the following:
1. A Laravel app that implements a JSON API

2. The Company would like to post the JSON from Chris to an endpoint of your choice. This JSON represents an Employee -> Supervisor relationship that looks like this:
{
          "Pete": "Nick",
          "Barbara": "Nick",
          "Nick": "Sophie",
          "Sophie": "Jonas"
}
In this case, Nick is a supervisor of Pete and Barbara, Sophie supervises Nick.

3. As a response to calling the endpoint, The Company would like to have a properly formatted JSON which reflects the employees hierarchy in a way, where the most senior employee is on the top of the JSON nested dictionary, and his supervisors are on the bottom. For instance, previous input would result in:
      {"Jonas": [
         {"Sophie": [
             {"Nick": [
                 {"Pete": []},
                 {"Barbara": []}
             ]}
]} ]}
4. We assume that the employee names are unique and the input hierarchy is valid. This means, there is always one boss on the top and the hierarchy does not contain loops (unless you would like to do a Bonus bullet point)

5. Don’t forget to Unit test the solution (with e.g. PHPUnit), so that The Company is sure everything works as she has specified
   The HR Operating System

Bonus points:

1. The Company would be especially happy if you would build a web app on top of this API, which would allow her to upload a text file with employee hierarchy and see a graph representation of it. You are free in picking whatever frameworks you see fit.

2. Sometimes Chris gives The Company nonsense hierarchies that contain loops. She would be grateful if you could detect them and tell her if such loops occurred.


## Project Info

There are 2 API endpoints, and an upload page.

API (POST or GET)

http://127.0.0.1:8000/api/index

http://127.0.0.1:8000/api/graph

Upload Page:

http://127.0.0.1:8000/upload

## Demo Video

Check demo_vid.mov for a demo of the webapp!
# hierarchyjson
